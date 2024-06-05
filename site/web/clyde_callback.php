<?php
require getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php';
require_once(getenv('CONFIG_LIB_DIR') . '/clyde_service.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');

function redirect_to_error($error_code) {
  $error_url = '/login?error_code=' . $error_code;
  if (isset($_SESSION['oauth2redirect'])) {
    $error_url .= '&redirect=' . $_SESSION['oauth2redirect'];
  }
  header('Location: ' . $error_url);
  exit;
}

session_start();
$clyde = new ClydeService();

// 1. Get the OAuth token
try {
  $token = $clyde->access_token($_GET['code'], $_GET['state']);
} catch (Exception $e) {
  log_exception($e);
  redirect_to_error($e->getMessage());
}

// 2. Get "me" from Clyde
$registrant = $clyde->get_registrant();

// 3. Check allowed access
$access_code = $clyde->registrant_allowed_access($registrant);
if ($access_code != null) {
  redirect_to_error($access_code);
}

// and if acccess is alllowed create a session etc
$badge_no = $registrant['ticket_number'];

$name = trim($registrant['badge']);
$name = ($name && strlen($name) > 0) ? $name : trim($registrant['preferred_name']);
$name = ($name && strlen($name) > 0) ? $name : trim($registrant['full_name']);

$email = trim($registrant['email']);

if (defined('CLYDE_ALLOWLIST') && !in_array($email, CLYDE_ALLOWLIST)) {
  redirect_to_error('not-in-allowlist');
}

if (!db_member_exists($badge_no)) {
  create_member($badge_no, $email, $name);
};
if (!db_identity_exists($badge_no)) {
  db_create_oauth_identity($badge_no, 'clyde', $registrant['id'], $email, json_encode($registrant));
};
make_session($badge_no);

if (isset($_SESSION['oauth2redirect'])) {
  $redirect = $_SESSION['oauth2redirect'];
  unset($_SESSION['oauth2redirect']);
  if (strpos($redirect, '/') !== 0) {
    // Don't allow open redirects
    // https://cheatsheetseries.owasp.org/cheatsheets/Unvalidated_Redirects_and_Forwards_Cheat_Sheet.html
    $redirect = '/';
  }
  header('Location: ' . $redirect);
} else {
  header('Location: /');
}
?>