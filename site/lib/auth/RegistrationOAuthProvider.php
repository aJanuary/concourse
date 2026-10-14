<?php

require_once(getenv('CONFIG_LIB_DIR') . '/oauth_service.php');
require_once(__DIR__ . '/AuthProvider.php');

// Logs in with an OAuth2 account on the registration system
class RegistrationOAuthProvider implements AuthProvider {
  public function id(): string {
    return 'registration-oauth';
  }

  public function display_name(): string {
    return 'Log in';
  }

  public function begin(string $redirect): void {
    $oauth = new OAuthService();
    $_SESSION['oauth2redirect'] = $redirect;
    header('Location: ' . $oauth->authorize_url());
    exit;
  }

  public function complete(array $params): Identity {
    $oauth = new OAuthService();

    try {
      $oauth->access_token($params['code'] ?? null, $params['state'] ?? null);
    } catch (Exception $e) {
      log_exception($e);
      throw new AuthFailure($e->getMessage());
    }

    $registrant = $oauth->get_registrant();

    $access_code = $oauth->registrant_allowed_access($registrant);
    if ($access_code != null) {
      throw new AuthFailure($access_code);
    }

    $name = trim($registrant['badge']);
    $name = ($name && strlen($name) > 0) ? $name : trim($registrant['preferred_name']);
    $name = ($name && strlen($name) > 0) ? $name : trim($registrant['full_name']);

    $badge_no = $registrant['ticket_number'];

    // Roles are derived from the ticket only when the member first appears, so
    // that later changes to a member's roles are not undone on each login.
    $roles = null;
    if (!db_member_exists($badge_no)) {
      $roles = [$registrant['attending_status'] == 'Virtual' ? 'online' : 'in-person'];
      if ($registrant['product_list_name'] == 'Hall Pass') {
        $roles[] = 'Hall Pass';
      }
      if (str_contains(strtolower($registrant['wsfs_status']), 'vote')) {
        $roles[] = 'wsfs';
      }
    }

    return new Identity(
      $badge_no,
      trim($registrant['email']),
      $name,
      null,
      $roles,
      ['oauth', $registrant['id'], json_encode($registrant)],
    );
  }
}
