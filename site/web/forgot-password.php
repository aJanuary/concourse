<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/mail.php');
require_once(getenv('CONFIG_LIB_DIR') . '/secure_links.php');

$path = preg_replace('/\.php$/', '', $_SERVER['PHP_SELF']);
$title = $path === '/set-password' ? 'Set password' : 'Forgot password';

if ($_REQUEST['token']) {
  $title = 'Set password';
  $token_data = verify_secure_link($path, $_REQUEST['token']);
  if ($token_data === 'invalid') {
    $error = 'The link you used is invalid. Please request a new link.';
  } else if ($token_data == 'expired') {
    $error = 'The link you used has expired. Please request a new link.';
  } else {
    $email = $token_data['email'] ?? null;
    $redirect = $token_data['redirect'] ?? '/';

    if ($_POST['phase'] === 'entered-password') {
      $password = $_POST['password'];
      $confirm_password = $_POST['confirm_password'];
      if ($password !== $confirm_password) {
        header('Location: ' . $path . '?token=' . urlencode($_REQUEST['token']) . '&error_code=passwords-did-not-match');
        exit();
      } else {
        db_set_password_auth($email, $password);
        $link = create_secure_link(
          '/login', [
          'phase' => 'select-user',
          'email' => $email,
          'redirect' => $redirect,
        ]);

        header('Location: ' . $link);
        exit();
      }
      // TODO: Check password strength
    } else {
      $phase = 'enter-password';
    }
  }
} else if ($_POST['email']) {
    $email = trim($_POST['email']);

    if ($path == 'set-password') {
      send_set_password_email($email, $_POST['redirect'] ?? '/');
    } else {
      send_forgot_password_email($email, $_POST['redirect'] ?? '/');
    }

    header('Location: ' . $path . '?email=' . urlencode($email) . '&redirect=' . urlencode($_POST['redirect'] ?? '/'));
    exit();
}

if ($_GET['error_code'] === 'passwords-did-not-match') {
  $error = 'The passwords you entered did not match. Please try again.';
}

render_header(
  $title,
  'Create a new password for your account on ' . CON_NAME . '.',
  ['Home' => '/', $title]
);

?>
<article>
  <h2><?php echo $title; ?></h2>
<?php
if ($error) {
?>
    <p class="error"><img src="resources/error.svg" alt="error" aria-label="error"><?php echo $error; ?></p>
<?php
} else if ($phase !== 'enter-password') {
?>
  <p>An email has been sent to <?php echo htmlspecialchars($_GET['email'] ?? 'your email address'); ?> with instructions on how to <?php echo $title === 'Set password' ? 'set a password' : 'reset your password'; ?>.</p>
  <p>If you do not receive an email within a few minutes, please check your spam folder or try again.</p>
<?php
}
?>
<?php
if ($phase === 'enter-password') {
?>
  <form action="<?php echo $path; ?>" method="POST" class="nu-form">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
    <input type="hidden" name="phase" value="entered-password">
    <div class="form-group">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
      <span class="error-message"></span>
    </div>
    <div class="form-group">
      <label for="confirm_password">Confirm password</label>
      <input id="confirm_password" name="confirm_password" type="password" required>
      <span class="error-message"></span>
    </div>
    <input type="submit" value="<?php echo $title; ?>">
  </form>
<?php
} else {
?>
  <form action="<?php echo $path; ?>" method="POST">
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect'] ?? '/'); ?>">
    <input type="submit" value="Send another email">
  </form>
<?php
}
?>
</article>
<?php
render_footer();
?>