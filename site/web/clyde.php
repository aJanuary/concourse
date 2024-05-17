<?php
require getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php';
require_once(getenv('CONFIG_LIB_DIR') . '/clyde_service.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

function make_session($email) {
  $session_id = sha1(rand());
  $expires_at = time() + 60*60*24*30;
  db_insert_session($session_id, $email, $expires_at);
  setcookie("session", $session_id, $expires_at, '/', '', true, true);
}

session_start();

try {
  $clyde = new ClydeService();

  // 1. Get the OAuth token
  $token = $clyde->access_token($_GET['code']);

  // 2. Get "me" from Clyde
  $registrant = $clyde->get_registrant();

  // 3. Check allowed access
  if ($clyde->registrant_allowed_access($registrant)) {
    // and if acccess is alllowed create a session etc
    $badge_id = $registrant['ticket_number'];    
    $name = $registrant['badge'] ?? $registrant['preferred_name'] ?? $registrant['full_name'];
    $email = $registrant['email'];

    $member_exists = db_member_exists($email);
    $identity_exists = db_identity_exists($email);

    if ($identity_exists && $member_exists) {
      $matches = db_validate_member_identity($email, 'clyde');
      if (!$matches) {
        throw new AuthorizationException("Clyde Information does not match member - possible duplicate email");
      }
    }

    if (!$member_exists) {
      db_create_member($badge_id, $name, $email, null);
    };
    if (!$identity_exists) {
      db_create_oauth_identity($badge_id, 'clyde', $registrant['id'], $email, json_encode($registrant));
    };
    make_session($email);
    header('Location: /');
  } else {
    header('Location: /login?error=clyde');
  }
} catch (Exception $e) {
  // Failed to get user details
  $err_code = log_exception($e);
  echo "Sorry, something has gone wrong. Please try again. If the problem persists, contact <a href=\"mailto:" . EMAIL . "?subject=Problem with member portal&body=Code: $err_code%0D%0A%0D%0AProblem:%0D%0A\">" . EMAIL . "</a> quoting the code \"$err_code\"\n";
  exit();
}

?>