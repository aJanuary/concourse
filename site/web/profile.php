<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

$phase = $_POST['phase'] ?? null;
if ($phase === 'change-password') {
  $current_password = $_POST['current_password'];
  $new_password = $_POST['new_password'];
  $confirm_new_password = $_POST['confirm_new_password'];

  if (!db_check_password_auth(get_current_user_email(), $current_password)) {
    header('Location: /profile?password_error_code=incorrect-password');
    exit();
  } else if ($new_password !== $confirm_new_password) {
    header('Location: /profile?password_error_code=passwords-did-not-match');
    exit();
  } else {
    db_set_password_auth(get_current_user_email(), $new_password);
    header('Location: /profile?password_changed=true');
    exit();
  }
}

$password_error_code = $_GET['password_error_code'];
if ($password_error_code === 'incorrect-password') {
  $password_error = 'The current password you entered was incorrect.';
} else if ($password_error_code === 'passwords-did-not-match') {
  $password_error = 'The new passwords you entered did not match.';
} else if ($password_error_code) {
  $password_error = 'An unknown error occurred.';
}

$password_changed = isset($_GET['password_changed']) && $_GET['password_changed'] === 'true';

render_header(
  'Profile',
  'Configure your profile on ' . CON_NAME . '.',
  ['Home' => '/', 'Profile']
);

?>
<article>
  <h2>Profile</h2>
  <p>If you want to change your badge name or other membership details, you can do so through the <a href="https://eastercon2026.org/members/portal" target="_blank">registration system</a>.</p>
  <h3>Set password</h3>
<?php
  if (conreg_user_owns_multiple_members(get_current_user_badge_no())) {
?>
  <p>Note, this will change the password for all members associated with your email address.</p>
<?php
  }
  if ($password_error) {
?>
    <p class="error"><img src="resources/error.svg" alt="error" aria-label="error"><?php echo htmlspecialchars($password_error); ?></p>
<?php
  }
  if ($password_changed) {
?>
    <p class="message"><img src="resources/success.svg" alt="success" aria-label="success">Your password has been changed successfully.</p>
<?php
  }
  if (db_member_has_password_auth(get_current_user_badge_no())) {
?>
    <form action="/profile" method="POST" class="nu-form">
      <input type="hidden" name="phase" value="change-password">

      <div class="form-group">
        <label for="current_password">Current password</label>
        <input id="current_password" name="current_password" type="password" required>
        <span class="error-message" aria-live="polite"></span>
      </div>

      <div class="form-group">
        <label for="new_password">New password</label>
        <input id="new_password" name="new_password" type="password" required>
        <span class="error-message" aria-live="polite"></span>
      </div>

      <div class="form-group">
        <label for="confirm_new_password">Confirm new password</label>
        <input id="confirm_new_password" name="confirm_new_password" type="password" required>
        <span class="error-message" aria-live="polite"></span>
      </div>

      <input type="submit" value="Change password">
    </form>
<?php
  } else {
?>
    <form action="/set-password" method="POST">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars(get_current_user_email()); ?>">
      <input type="hidden" name="redirect" value="/profile">
      <p>You do not currently have a password set.</p>
      <input type="submit" value="Set password">
    </form>
<?php
  }
?>
</article>
<?php
render_footer();
?>