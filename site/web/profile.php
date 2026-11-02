<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/login_throttle.php');

$badge_no = get_current_user_badge_no();
$email = get_current_user_email();

// A session from the oidc provider has no portal password to change
$local = auth_get_provider('local');
$is_oidc = auth_session_is_oidc();
$can_change_password = $local !== null && !$is_oidc;
$password_change_url = $is_oidc ? OIDC_PASSWORD_CHANGE_URL : '';

$errors = [
  'incorrect-password' => 'The current password you entered was incorrect.',
  'mismatch' => 'The new passwords you entered did not match.',
  'too-short' => 'Your new password must be at least ' . LocalProvider::MIN_PASSWORD_LENGTH . ' characters long.',
  'too-many-attempts' => LocalProvider::ERRORS['too-many-attempts'],
];

if ($can_change_password && ($_POST['phase'] ?? '') === 'change-password') {
  $new_password = (string) ($_POST['new_password'] ?? '');
  $error = null;
  if (login_throttle_blocked('password', $email)) {
    $error = 'too-many-attempts';
  } else if (!db_check_password_auth($email, (string) ($_POST['current_password'] ?? ''))) {
    login_throttle_hit('password', $email);
    $error = 'incorrect-password';
  } else if ($new_password !== (string) ($_POST['confirm_new_password'] ?? '')) {
    $error = 'mismatch';
  } else if (strlen($new_password) < LocalProvider::MIN_PASSWORD_LENGTH) {
    $error = 'too-short';
  }

  if ($error === null) {
    db_set_password_auth($email, $new_password);
    header('Location: /profile?password_changed=1');
  } else {
    header('Location: /profile?error=' . $error);
  }
  exit;
}

$error = $errors[$_GET['error'] ?? ''] ?? null;
$password_changed = isset($_GET['password_changed']);
$roles = array_diff(db_get_member_role_names($badge_no), ['default']);
$owns_several = count(auth_switchable_members()) > 1;

render_page('pages/profile.html.twig', [
  'title' => 'Profile',
  'description' => 'Your profile on ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Profile'],
  'badge_no' => $badge_no,
  'name' => get_current_user_name(),
  'email' => $email,
  'roles' => $roles,
  'owns_several' => $owns_several,
  'can_change_password' => $can_change_password,
  'has_password' => $can_change_password && db_email_has_password_auth($email),
  'password_change_url' => $password_change_url,
  'error' => $error,
  'password_changed' => $password_changed,
  'min_password_length' => LocalProvider::MIN_PASSWORD_LENGTH,
]);
