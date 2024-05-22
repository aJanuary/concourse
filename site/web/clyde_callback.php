<?php
require getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php';
require_once(getenv('CONFIG_LIB_DIR') . '/clyde_service.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');

$clyde = new ClydeService();

// 1. Get the OAuth token
$token = $clyde->access_token($_GET['code']);

// 2. Get "me" from Clyde
$registrant = $clyde->get_registrant();

// 3. Check allowed access
if ($clyde->registrant_allowed_access($registrant)) {
  // and if acccess is alllowed create a session etc
  $badge_id = $registrant['ticket_number'];

  $name = trim($registrant['badge']);
  $name = ($name && strlen($name) > 0) ? $name : trim($registrant['preferred_name']);
  $name = ($name && strlen($name) > 0) ? $name : trim($registrant['full_name']);
  
  $email = $registrant['email'];

  $member_exists = db_member_exists($email);
  $identity_exists = db_identity_exists($email);

  if ($identity_exists && $member_exists) {
    // TODO: this needs to be changed to allow for non-unique emails
    $matches = db_validate_member_identity($email, 'clyde');
    if (!$matches) {
      throw new AuthorizationException("Clyde Information does not match member - possible duplicate email");
    }
  }

  if (!$member_exists) {
    create_member($badge_id, $email, $name);
  };
  if (!$identity_exists) {
    db_create_oauth_identity($badge_id, 'clyde', $registrant['id'], $email, json_encode($registrant));
  };
  make_session($email);
  header('Location: /');
} else {
  header('Location: /login?error=clyde');
}

?>