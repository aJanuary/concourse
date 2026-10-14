<?php
use chillerlan\QRCode\QRCode;

require getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php';
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');

$provider = auth_get_provider('registration-oauth');
if ($provider === null) {
  render_404();
}

session_start();

if (!array_key_exists('oauth2redirect', $_SESSION)) {
  render_header(
    'Log in',
    'Log in to the member portal for ' . CON_NAME . '.',
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
    <p>Or, if you are started the log-in process on your mobile device, you can scan the QR code below with your device's camera app:</p>
    <?php
      echo '<img id="qr-code" src="'.(new QRCode)->render(ROOT_URL . $_SERVER['REQUEST_URI']).'" alt="QR Code" />';
    ?>
  </article>
<?php
  render_footer();
  exit;
} else {
  $redirect = $_SESSION['oauth2redirect'];
  try {
    finish_login($provider->complete($_GET), $redirect);
  } catch (AuthFailure $e) {
    redirect_to_login_error($e->getMessage(), $redirect);
  }
}
?>