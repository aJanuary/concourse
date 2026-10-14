<?php
// A minimal OpenID Connect provider for trying the portal's `oidc` login
// provider without a real identity provider. Development only: it approves
// every login without asking for credentials.
//
// docker-compose.oidc.yml runs it as http://localhost:8090, reachable from
// both the browser and the web container. Claims returned from userinfo are
// read from claims.json in the data directory (secrets/mock-oidc/ under
// compose) on every request, so edit it between logins.
//
// Logging out of the portal redirects to /logout here when the portal has
// `provider_logout = true`, which ends the matching provider session.
//
// Browse to http://localhost:8090/ to see the provider sessions it has
// issued. Logging one out POSTs a back-channel logout token to the portal
// (MOCK_OIDC_BACKCHANNEL_URL, which compose points at the portal's
// /oidc/logout). "Log out all of this user's sessions" sends a token with
// only a sub. Create no-sid in the data directory to issue ID tokens without
// a sid, which makes every logout sub-only.

require_once('/srv/lib/vendor/autoload.php');

use Firebase\JWT\JWT;

define('DIR', rtrim(getenv('MOCK_OIDC_DIR') ?: '/tmp/mock-oidc', '/'));
@mkdir(DIR, 0700, true);

$issuer = getenv('MOCK_OIDC_ISSUER') ?: 'http://localhost:8090';
$kid = 'mock-key';

if (!file_exists(DIR . '/key.pem')) {
  $key = openssl_pkey_new(['private_key_bits' => 2048]);
  openssl_pkey_export($key, $pem);
  file_put_contents(DIR . '/key.pem', $pem);
}
if (!file_exists(DIR . '/claims.json')) {
  file_put_contents(DIR . '/claims.json', json_encode([
    'sub' => 'user-1', 'member-number' => '0101', 'email' => 'mock@example.com',
    'name' => 'Mock Member', 'full-name' => 'Mock Q. Member', 'is-in-person' => true, 'is-online' => false,
  ], JSON_PRETTY_PRINT));
}
$pem = file_get_contents(DIR . '/key.pem');
$backchannel_url = getenv('MOCK_OIDC_BACKCHANNEL_URL') ?: 'http://localhost:8080/oidc/logout';
$details = openssl_pkey_get_details(openssl_pkey_get_private($pem));
$b64 = fn($s) => rtrim(strtr(base64_encode($s), '+/', '-_'), '=');

$sessions_file = DIR . '/sessions.json';
$load_sessions = fn() => is_file($sessions_file) ? json_decode(file_get_contents($sessions_file), true) : [];
$save_sessions = fn($sessions) => file_put_contents($sessions_file, json_encode(array_values($sessions)));

$logout_token = fn($client_id, $sub, $sid) => JWT::encode([
  'iss' => $issuer, 'aud' => $client_id, 'iat' => time(), 'jti' => bin2hex(random_bytes(8)),
  'sub' => $sub, 'sid' => $sid,
  'events' => ['http://schemas.openid.net/event/backchannel-logout' => new stdClass()],
], $pem, 'RS256', $kid);

// Returns the portal's HTTP status, or the error if it was unreachable
$send_backchannel = function ($token) use ($backchannel_url) {
  $ch = curl_init($backchannel_url);
  curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query(['logout_token' => $token]), CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5]);
  $body = curl_exec($ch);
  $result = $body === false ? curl_error($ch) : curl_getinfo($ch, CURLINFO_HTTP_CODE) . ' ' . $body;
  return $result;
};

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
header('Content-Type: application/json');

switch ($path) {
  case '/.well-known/openid-configuration':
    echo json_encode([
      'issuer' => $issuer,
      'authorization_endpoint' => "$issuer/authorize",
      'token_endpoint' => "$issuer/token",
      'userinfo_endpoint' => "$issuer/userinfo",
      'jwks_uri' => "$issuer/jwks",
      'end_session_endpoint' => "$issuer/logout",
      'response_types_supported' => ['code'],
      'subject_types_supported' => ['public'],
      'id_token_signing_alg_values_supported' => ['RS256'],
      'token_endpoint_auth_methods_supported' => ['client_secret_basic', 'client_secret_post'],
      'backchannel_logout_supported' => true,
      'backchannel_logout_session_supported' => true,
    ]);
    break;

  case '/jwks':
    echo json_encode(['keys' => [[
      'kty' => 'RSA', 'use' => 'sig', 'alg' => 'RS256', 'kid' => $kid,
      'n' => $b64($details['rsa']['n']), 'e' => $b64($details['rsa']['e']),
    ]]]);
    break;

  case '/authorize':
    $code = bin2hex(random_bytes(8));
    file_put_contents(DIR . "/code-$code", json_encode(['nonce' => $_GET['nonce'] ?? null, 'client_id' => $_GET['client_id'] ?? null]));
    header('Content-Type: text/plain');
    header('Location: ' . $_GET['redirect_uri'] . '?' . http_build_query(['code' => $code, 'state' => $_GET['state'] ?? '']));
    http_response_code(302);
    break;

  case '/token':
    $file = DIR . '/code-' . ($_POST['code'] ?? '');
    if (!is_file($file)) {
      http_response_code(400);
      echo json_encode(['error' => 'invalid_grant']);
      break;
    }
    $grant = json_decode(file_get_contents($file), true);
    unlink($file);
    $claims = json_decode(file_get_contents(DIR . '/claims.json'), true);
    $sid = 'sid-' . bin2hex(random_bytes(4));
    $sessions = $load_sessions();
    $sessions[] = ['id' => $sid, 'sid' => file_exists(DIR . '/no-sid') ? null : $sid, 'sub' => $claims['sub'], 'client_id' => $grant['client_id'], 'name' => $claims['name'] ?? $claims['sub'], 'created' => date('H:i:s')];
    $save_sessions($sessions);
    $access = bin2hex(random_bytes(16));
    $id_token = JWT::encode([
      'iss' => $issuer, 'sub' => $claims['sub'], 'aud' => $grant['client_id'],
      'iat' => time(), 'exp' => time() + 300, 'nonce' => $grant['nonce'],
      'sid' => file_exists(DIR . '/no-sid') ? null : $sid,
      'mock_id' => $sid,
      'at_hash' => $b64(substr(hash('sha256', $access, true), 0, 16)),
    ], $pem, 'RS256', $kid);
    echo json_encode(['access_token' => $access, 'token_type' => 'Bearer', 'expires_in' => 300, 'id_token' => $id_token]);
    break;

  case '/userinfo':
    echo file_get_contents(DIR . '/claims.json');
    break;

  case '/logout':
    // RP-initiated logout: ends the provider session named by the hint and
    // sends the browser on. No back-channel logout, as the portal is the
    // one logging out.
    $parts = explode('.', $_GET['id_token_hint'] ?? '');
    $hint = count($parts) === 3 ? json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true) : null;
    $save_sessions(array_filter($load_sessions(), fn($x) => $x['id'] !== ($hint['mock_id'] ?? null)));
    header('Location: ' . ($_GET['post_logout_redirect_uri'] ?? '/'));
    http_response_code(302);
    break;

  case '/':
    header('Content-Type: text/html; charset=utf-8');
    $message = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $sessions = $load_sessions();
      $subject = $_POST['sub'] ?? null;
      $id = $_POST['id'] ?? null;
      $targets = array_filter($sessions, fn($x) => $id !== null ? $x['id'] === $id : $x['sub'] === $subject);
      if ($targets) {
        $first = reset($targets);
        $token = $logout_token($first['client_id'], $first['sub'], $id !== null ? $first['sid'] : null);
        $message = 'Portal replied: ' . $send_backchannel($token);
        $save_sessions(array_filter($sessions, fn($x) => !in_array($x, $targets, true)));
      } else {
        $message = 'No such session';
      }
    }
    $h = fn($v) => htmlspecialchars((string) $v);
    echo '<!doctype html><title>Mock identity provider</title><body style="font-family:sans-serif;max-width:50rem;margin:2rem auto">';
    echo '<h1>Mock identity provider</h1><p>Sessions issued since the last logout. Edit <code>claims.json</code> to change who logs in next.</p>';
    if ($message !== null) {
      echo '<p><strong>' . $h($message) . '</strong></p>';
    }
    $sessions = $load_sessions();
    echo $sessions ? '<table cellpadding="6"><tr><th>User</th><th>sid</th><th>Since</th><th></th></tr>' : '<p>No sessions.</p>';
    foreach ($sessions as $x) {
      echo '<tr><td>' . $h($x['name']) . ' (' . $h($x['sub']) . ')</td><td>' . $h($x['sid'] ?? '(none)') . '</td><td>' . $h($x['created']) . '</td><td>'
        . '<form method="post" style="display:inline"><input type="hidden" name="id" value="' . $h($x['id']) . '"><button>Log out this session</button></form> '
        . '<form method="post" style="display:inline"><input type="hidden" name="sub" value="' . $h($x['sub']) . '"><button>Log out all of this user\'s sessions</button></form></td></tr>';
    }
    echo $sessions ? '</table>' : '';
    break;

  default:
    http_response_code(404);
}
