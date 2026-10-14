<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php');
require_once(__DIR__ . '/AuthProvider.php');

use Jumbojett\OpenIDConnectClient;

// Logs in through any OpenID Connect provider, using the authorization code
// flow, and ends portal sessions when the provider sends a back-channel
// logout.
//
// The browser comes back to /oidc/callback, not to /login, so the pending
// redirect is kept in the PHP session alongside the library's state and nonce.
// A callback with no pending login was opened in a different browser to the
// one that started it.
//
// Member fields and roles come from userinfo claims, named in the [oidc]
// config: `claims[...]` and `role_map[...]`.
class OidcProvider implements AuthProvider {
  const REDIRECT_KEY = 'oidc_redirect';

  public function id(): string {
    return 'oidc';
  }

  public function display_name(): string {
    return 'Log in with your account';
  }

  public function begin(string $redirect): void {
    $this->start_session();
    $_SESSION[self::REDIRECT_KEY] = $redirect;
    $this->client()->authenticate();
    exit;
  }

  public function login_in_progress(): bool {
    $this->start_session();
    return isset($_SESSION[self::REDIRECT_KEY]);
  }

  public function pending_redirect(): string {
    $this->start_session();
    return $_SESSION[self::REDIRECT_KEY] ?? '/';
  }

  // $params is the callback's query string
  public function complete(array $params): Identity {
    // authenticate() would start a new login rather than fail
    if (!isset($params['code']) && !isset($params['error'])) {
      throw new AuthFailure('invalid-state');
    }

    $client = $this->client();
    try {
      $client->authenticate();
      $userinfo = json_decode(json_encode($client->requestUserInfo()), true);
      $sid = $client->getVerifiedClaims('sid');
      $sub = $client->getVerifiedClaims('sub');
      $id_token = $client->getIdToken();
    } catch (Exception $e) {
      log_exception($e);
      throw new AuthFailure('invalid-state');
    }

    return $this->identity_from_claims($userinfo, self::string_or_null($sid), self::string_or_null($sub), self::string_or_null($id_token));
  }

  // Sends the browser to the provider's end-session endpoint (RP-initiated
  // logout), which returns it to the login page. Returns if there is nothing
  // to do, so the caller can carry on to the login page itself.
  public function sign_out(?string $id_token): void {
    if (!OIDC_PROVIDER_LOGOUT || $id_token === null) {
      return;
    }
    try {
      $this->client()->signOut($id_token, ROOT_URL . '/login');
    } catch (Exception $e) {
      // The provider may not advertise an end-session endpoint
      log_exception($e);
    }
  }

  // The `sid` and `sub` of a verified back-channel logout token, or null if
  // the token does not verify
  public function verified_logout_claims(): ?array {
    try {
      $client = $this->client();
      if (!$client->verifyLogoutToken()) {
        return null;
      }
      return [
        'sid' => self::string_or_null($client->getVerifiedClaims('sid')),
        'sub' => self::string_or_null($client->getVerifiedClaims('sub')),
      ];
    } catch (Exception $e) {
      log_exception($e);
      return null;
    }
  }

  private static function string_or_null($value): ?string {
    return is_string($value) && $value !== '' ? $value : null;
  }

  public function identity_from_claims(array $claims, ?string $sid, ?string $sub, ?string $id_token): Identity {
    $field = function ($name) use ($claims) {
      $claim = OIDC_CLAIMS[$name] ?? '';
      $value = $claim === '' ? null : ($claims[$claim] ?? null);
      return is_scalar($value) && trim((string) $value) !== '' ? trim((string) $value) : null;
    };

    $badge_no = $field('badge_no');
    $email = $field('email');
    $name = $field('name');
    if ($badge_no === null || $email === null || $name === null) {
      throw new AuthFailure('missing-claims');
    }

    $managed_roles = array_values(array_unique(array_values(OIDC_ROLE_MAP)));
    $roles = [];
    foreach (OIDC_ROLE_MAP as $claim => $role) {
      if (self::is_truthy($claims[$claim] ?? null)) {
        $roles[] = $role;
      }
    }

    return new Identity($badge_no, $email, $name, $field('real_name'), array_values(array_unique($roles)), $managed_roles, $sid, $sub, $id_token);
  }

  // Claims are booleans, numbers or strings depending on the provider
  public static function is_truthy($value): bool {
    if (is_bool($value)) {
      return $value;
    }
    if (is_int($value) || is_float($value)) {
      return $value != 0;
    }
    return is_string($value) && in_array(strtolower(trim($value)), ['true', '1', 'yes', 'on'], true);
  }

  private function client(): OpenIDConnectClient {
    require_config('oidc', 'provider_url', 'client_id', 'client_secret');

    $client = new OpenIDConnectClient(OIDC_PROVIDER_URL, OIDC_CLIENT_ID, OIDC_CLIENT_SECRET);
    $client->setRedirectURL(ROOT_URL . '/oidc/callback');
    $client->addScope(array_values(array_diff(OIDC_SCOPES, ['openid'])));
    // Off only when the config says so, for a development provider with a
    // self-signed certificate
    $client->setVerifyHost(OIDC_VERIFY_TLS);
    $client->setVerifyPeer(OIDC_VERIFY_TLS);
    return $client;
  }

  private function start_session(): void {
    if (session_status() !== PHP_SESSION_NONE) {
      return;
    }
    // Lax still sends the cookie on the redirect back from the provider
    session_set_cookie_params([
      'secure' => IS_HTTPS,
      'httponly' => true,
      'samesite' => 'Lax',
    ]);
    session_start();
  }
}
