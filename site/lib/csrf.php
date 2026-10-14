<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');

// The session cookie is SameSite=None over HTTPS so ConClar can send it, which
// means another site can make a member's browser post to the portal with it.
// Every form therefore carries a token that only pages on this site can read:
// a double-submit of a random cookie. Over HTTPS the __Host- prefix stops a
// sibling subdomain from planting a cookie whose token it knows.
define('CSRF_COOKIE_NAME', IS_HTTPS ? '__Host-csrf' : 'csrf');

// The token for this browser, starting its cookie if it has none. Must first
// be called before any output; render_header() does so. A $purpose gives a
// separate token, for one that leaves the site such as an OAuth state.
function csrf_token($purpose = 'form') {
  $secret = $_COOKIE[CSRF_COOKIE_NAME] ?? '';
  if (!preg_match('/^[0-9a-f]{64}$/', $secret)) {
    $secret = bin2hex(random_bytes(32));
    setcookie(CSRF_COOKIE_NAME, $secret, [
      'path' => '/',
      'secure' => IS_HTTPS,
      'httponly' => true,
      // Lax so the cookie comes back on the redirect from an OAuth provider
      'samesite' => 'Lax',
    ]);
    $_COOKIE[CSRF_COOKIE_NAME] = $secret;
  }
  return hash_hmac('sha256', $purpose, $secret);
}

function csrf_field() {
  return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_token_valid($token, $purpose = 'form') {
  return is_string($token) && isset($_COOKIE[CSRF_COOKIE_NAME]) && hash_equals(csrf_token($purpose), $token);
}

// Stops a POST that did not come from one of the portal's own pages. Takes the
// token from the form or, for htmx, the X-CSRF-Token header.
function csrf_check() {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
  }
  if (csrf_token_valid($_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    return;
  }

  require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
  http_response_code(403);
  render_header('Form expired', 'Form expired.', ['Home' => '/', 'Form expired']);
?>
  <article>
    <h3>Form expired</h3>
    <p>We could not accept that form. Please go back, reload the page and try again.</p>
  </article>
<?php
  render_footer();
  exit;
}
