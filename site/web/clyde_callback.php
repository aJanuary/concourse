<?php
require getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php';
require_once(getenv('CONFIG_LIB_DIR') . '/clyde_service.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

function redirect_to_error($error_code) {
  $error_url = '/login?error_code=' . $error_code;
  if (isset($_SESSION['oauth2redirect'])) {
    $error_url .= '&redirect=' . $_SESSION['oauth2redirect'];
  }
  header('Location: ' . $error_url);
  exit;
}

$clyde = new ClydeService();

if (!array_key_exists('oauth2redirect', $_SESSION)) {
  if (array_key_exists('session', $_COOKIE)) {
    session_destroy();
    header('Location: /');
    exit;
  }
  
  render_header(
    'Log in',
    'Log in to the member portal for Glasgow 2024, A Worldcon for Our Futures.',
    ['Home' => '/', 'Log in']
  );

?>
  <article>
    <h2>Log in</h2>
    <h3>Your princess is in another castle!</h3>
    <p>It looks like the link opened in a different browser to the one you started logging in with.</p>
    <p>Often this is because your e-mail client is using an embedded browser rather than opening using your system browser.</p>
    <p>Copy the URL below, switch to your regular browser, and paste the URL into the URL bar:</p>
    <p id="url"><input type="text" readonly value="<?php echo ROOT_URL . $_SERVER['REQUEST_URI']; ?>"/></p>
    <script>
      const $url = document.getElementById("url");
      const $urlInput = $url.querySelector("input");
      $urlInput.addEventListener('focus', () => $urlInput.select());

      if (navigator.clipboard) {
        const $copy = document.createElement('button');
        $copy.textContent = 'Copy';
        $copy.addEventListener('click', () => {
          navigator.clipboard.writeText($urlInput.value);
          $urlInput.select();
          $copy.textContent = 'Copied!';
          setTimeout(() => $copy.textContent = 'Copy', 2000);
        });
        $url.appendChild($copy);
      }
    </script>
  </article>
<?php
  render_footer();
  exit;
} else {
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
    if ($registrant['attending_status'] == 'Virtual') {
      db_add_member_role($badge_no, 'online');
    } else {
      db_add_member_role($badge_no, 'in-person');
    }
    if (str_contains(strtolower($registrant['wsfs_status']), 'vote')) {
      db_add_member_role($badge_no, 'wsfs');
    }
  } else {
    update_member($badge_no, $email, $name);
  }
  if (!db_identity_exists($badge_no)) {
    db_create_oauth_identity($badge_no, 'clyde', $registrant['id'], $email, json_encode($registrant));
  }

  make_session($badge_no);

  $redirect = $_SESSION['oauth2redirect'];
  session_destroy();
  if (strpos($redirect, '/') !== 0) {
    // Don't allow open redirects
    // https://cheatsheetseries.owasp.org/cheatsheets/Unvalidated_Redirects_and_Forwards_Cheat_Sheet.html
    $redirect = '/';
  }
  header('Location: ' . $redirect);
}
?>