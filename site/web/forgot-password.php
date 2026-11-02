<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/secure_links.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/login_throttle.php');
require_once(getenv('CONFIG_LIB_DIR') . '/csrf.php');

csrf_check();

$provider = auth_get_provider('local');
if ($provider === null) {
  render_404();
}

// set-password.php includes this page
$kind = ($mode ?? 'forgot') === 'set' ? 'set' : 'reset';
$path = $kind === 'set' ? '/set-password' : '/forgot-password';
$title = $kind === 'set' ? 'Set password' : 'Forgot password';

$errors = [
  'mismatch' => 'The passwords you entered did not match. Please try again.',
  'too-short' => 'Your password must be at least ' . LocalProvider::MIN_PASSWORD_LENGTH . ' characters long.',
  'too-many-attempts' => LocalProvider::ERRORS['too-many-attempts'],
];

// Every POST redirects to a GET page, so refreshing never resubmits a form
function redirect_to_page($path, $query) {
  header('Location: ' . $path . '?' . http_build_query($query));
  exit;
}

$error = $errors[$_GET['error'] ?? ''] ?? null;
$phase = 'request';
$email = null;
$redirect = '/';

$token = $_POST['token'] ?? $_GET['token'] ?? null;
if ($token !== null) {
  $title = 'Set password';
  $token_data = verify_secure_link($path, (string) $token);
  if ($token_data === 'invalid') {
    $error = 'The link you used is invalid. Please request a new link.';
  } else if ($token_data === 'expired') {
    $error = 'The link you used has expired. Please request a new link.';
  } else {
    $email = $token_data['email'] ?? '';
    $redirect = $token_data['redirect'] ?? '/';
    $phase = 'enter-password';

    if (($_POST['phase'] ?? '') === 'entered-password') {
      $password = $_POST['password'] ?? '';
      if ($password !== ($_POST['confirm_password'] ?? '')) {
        redirect_to_page($path, ['token' => $token, 'error' => 'mismatch']);
      } else if (strlen($password) < LocalProvider::MIN_PASSWORD_LENGTH) {
        redirect_to_page($path, ['token' => $token, 'error' => 'too-short']);
      }
      // Following the emailed link shows control of the address, so the
      // member is logged in without a further step.
      db_set_password_auth($email, $password);
      try {
        finish_login($provider->identity_for_verified_email($email, $redirect), $redirect);
      } catch (AuthFailure $e) {
        redirect_to_login_error($e->getMessage(), $redirect);
      }
    }
  }
} else if (isset($_POST['email'])) {
  $email = trim($_POST['email']);
  $redirect = $_POST['redirect'] ?? '/';
  $query = ['email' => $email, 'redirect' => $redirect];

  if (login_throttle_blocked('password-email', $email)) {
    $query['error'] = 'too-many-attempts';
  } else {
    login_throttle_hit('password-email', $email);
    $provider->send_password_email($email, $redirect, $kind);
    $query['sent'] = '1';
  }
  redirect_to_page($path, $query);
} else if (isset($_GET['email'])) {
  $email = trim($_GET['email']);
  $redirect = $_GET['redirect'] ?? '/';
  if (isset($_GET['sent'])) {
    $phase = 'sent';
  }
}

render_page('pages/forgot-password.html.twig', [
  'title' => $title,
  'description' => 'Create a new password for your account on ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', $title],
  'error' => $error,
  'phase' => $phase,
  'kind' => $kind,
  'path' => $path,
  'token' => $token,
  'email' => $email,
  'redirect' => $redirect,
  'min_password_length' => LocalProvider::MIN_PASSWORD_LENGTH,
]);
