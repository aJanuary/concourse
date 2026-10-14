<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
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

render_header(
  'Set a password',
  'Choose whether to set a password for ' . CON_NAME . '.',
  ['Home' => '/', 'Set a password']
);
?>
<article>
  <h2>Would you like to set a password?</h2>
  <p>You are logged in with a code sent to <?php echo htmlspecialchars($email); ?>. If you set a password, you can use it to log in next time instead of waiting for a code.</p>
  <p>We will email you a link to choose your password.</p>
  <form action="/set-password" method="post">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
    <button type="submit" class="button">Email me a link to set a password</button>
  </form>
  <form action="/password-prompt" method="post">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
    <button type="submit" class="button link">No thanks, don't ask again</button>
  </form>
  <p>You can set a password later from your profile.</p>
</article>
<?php
render_footer();
