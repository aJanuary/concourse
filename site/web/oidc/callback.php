<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/oidc_routes.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

use chillerlan\QRCode\QRCode;

$provider = oidc_provider_or_404();

if (!$provider->login_in_progress()) {
  $url = ROOT_URL . $_SERVER['REQUEST_URI'];
  render_header(
    'Log in',
    'Log in to the member portal for ' . CON_NAME . '.',
    ['Home' => '/', 'Log in']
  );
?>
  <article>
    <h2>Log in</h2>
    <h3>Your login link opened in a different browser</h3>
    <p>It looks like the link opened in a different browser to the one you started logging in with.</p>
    <p>Often this is because your email client is using an embedded browser rather than opening using your system browser.</p>
    <p>Copy the URL below, switch to your regular browser, and paste the URL into the URL bar:</p>
    <p id="url"><input type="text" readonly value="<?php echo htmlspecialchars($url); ?>"/></p>
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
    <p>Or, if you started logging in on a different device, you can scan the QR code below with that device's camera app:</p>
    <img id="qr-code" src="<?php echo htmlspecialchars((new QRCode)->render($url)); ?>" alt="QR Code" />
  </article>
<?php
  render_footer();
  exit;
}

$redirect = $provider->pending_redirect();
try {
  finish_login($provider->complete($_GET), $redirect);
} catch (AuthFailure $e) {
  redirect_to_login_error($e->getMessage(), $redirect);
}
