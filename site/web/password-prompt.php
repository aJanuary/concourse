<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');

$redirect = (string) ($_POST['redirect'] ?? $_GET['redirect'] ?? '/');
if (!is_valid_redirect($redirect)) {
  $redirect = '/';
}

$email = get_current_user_email();
if (auth_get_provider('local') === null
  || auth_session_is_oidc()
  || db_email_has_password_auth($email)
  || db_password_prompt_dismissed($email)) {
  header('Location: ' . $redirect);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  db_dismiss_password_prompt($email);
  header('Location: ' . $redirect);
  exit;
}

render_page('pages/password-prompt.html.twig', [
  'title' => 'Set a password',
  'description' => 'Choose whether to set a password for ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Set a password'],
  'email' => $email,
  'redirect' => $redirect,
]);
