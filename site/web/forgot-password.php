<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/secure_links.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/login_throttle.php');
require_once(getenv('CONFIG_LIB_DIR') . '/csrf.php');

csrf_check();

$provider = auth_get_provider('local');
if ($provider === null) {
  render_404();
  exit;
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

render_header(
  $title,
  'Create a new password for your account on ' . CON_NAME . '.',
  ['Home' => '/', $title]
);

?>
<article>
  <h2><?php echo $title; ?></h2>
<?php
if ($error !== null) {
?>
  <p class="error"><img src="/resources/error.svg" alt="error" aria-label="error"><?php echo $error; ?></p>
<?php
}
if ($phase === 'enter-password') {
?>
  <form action="<?php echo $path; ?>" method="POST" class="nu-form">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <input type="hidden" name="phase" value="entered-password">
    <div class="form-group">
      <label for="email">Email</label>
      <input id="email" type="email" value="<?php echo htmlspecialchars($email); ?>" autocomplete="username" disabled>
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="new-password" minlength="<?php echo LocalProvider::MIN_PASSWORD_LENGTH; ?>" required>
    </div>
    <div class="form-group">
      <label for="confirm_password">Confirm password</label>
      <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required>
    </div>
    <button type="submit" class="button login">Set password</button>
  </form>
<?php
} else if ($phase === 'sent') {
?>
  <p>If <?php echo htmlspecialchars($email); ?> belongs to a member, an email has been sent to it with instructions on how to <?php echo $kind === 'set' ? 'set a password' : 'reset your password'; ?>.</p>
  <p>If you do not receive an email within a few minutes, please check your spam folder or try again.</p>
  <form action="<?php echo $path; ?>" method="POST">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
    <button type="submit" class="button login">Send another email</button>
  </form>
<?php
} else if ($token === null) {
?>
  <form action="<?php echo $path; ?>" method="POST" class="nu-form">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
    <div class="form-group">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" autocomplete="username" required>
    </div>
    <button type="submit" class="button login">Send email</button>
  </form>
<?php
}
?>
</article>
<?php
render_footer();
