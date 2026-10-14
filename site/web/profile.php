<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
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

render_header(
  'Profile',
  'Your profile on ' . CON_NAME . '.',
  ['Home' => '/', 'Profile']
);
?>
<article>
  <h2>Profile</h2>
  <dl class="profile-details">
    <dt>Badge number</dt>
    <dd><?php echo htmlspecialchars($badge_no); ?></dd>
    <dt>Badge name</dt>
    <dd><?php echo htmlspecialchars(get_current_user_name()); ?></dd>
    <dt>Email</dt>
    <dd><?php echo htmlspecialchars($email); ?></dd>
    <?php if ($roles) { ?>
      <dt>Roles</dt>
      <dd><?php echo htmlspecialchars(implode(', ', $roles)); ?></dd>
    <?php } ?>
  </dl>
  <?php if ($owns_several) { ?>
    <p>Other members share this email address. <a href="/switch-member">Switch member</a></p>
  <?php } ?>

  <?php if ($can_change_password) { ?>
    <h3>Change password</h3>
    <?php if ($owns_several) { ?>
      <p>This changes the password for all members with this email address.</p>
    <?php } ?>
    <?php if ($error !== null) { ?>
      <p class="error" role="alert"><img src="/resources/error.svg" alt="error"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>
    <?php if ($password_changed) { ?>
      <p class="message" role="status">Your password has been changed.</p>
    <?php } ?>
    <?php if (db_email_has_password_auth($email)) { ?>
      <form action="/profile" method="post" class="nu-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="phase" value="change-password">
        <div class="form-group">
          <label for="current_password">Current password</label>
          <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
          <span class="error-message" aria-live="polite"></span>
        </div>
        <div class="form-group">
          <label for="new_password">New password</label>
          <input id="new_password" name="new_password" type="password" autocomplete="new-password" minlength="<?php echo LocalProvider::MIN_PASSWORD_LENGTH; ?>" required>
          <span class="error-message" aria-live="polite"></span>
        </div>
        <div class="form-group">
          <label for="confirm_new_password">Confirm new password</label>
          <input id="confirm_new_password" name="confirm_new_password" type="password" autocomplete="new-password" required>
          <span class="error-message" aria-live="polite"></span>
        </div>
        <button type="submit" class="button">Change password</button>
      </form>
    <?php } else { ?>
      <p>You do not have a password yet. You can log in with a code sent to your email, or set a password.</p>
      <form action="/set-password" method="post">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <input type="hidden" name="redirect" value="/profile">
        <button type="submit" class="button">Set password</button>
      </form>
    <?php } ?>
  <?php } else if ($password_change_url !== '') { ?>
    <h3>Password</h3>
    <p>Your password is managed by your login provider.</p>
    <p><a href="<?php echo htmlspecialchars($password_change_url); ?>" class="button">Change password</a></p>
  <?php } ?>
</article>
<?php
render_footer();
