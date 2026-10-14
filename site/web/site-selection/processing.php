<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

// Square sends members here after they pay. Their voter key is assigned when
// Square's webhook arrives, which may be a little later, so this waits for it.
check_permission('see-site-selection');

$support_email = htmlspecialchars(SUPPORT_EMAIL);

render_header(
  'Processing payment',
  'Your site selection payment is being processed.',
  ['Home' => '/', 'Site selection' => '/site-selection', 'Processing']
);
?>
<article>
  <h2>Processing your payment</h2>
  <p>Please wait while we confirm your payment&hellip;</p>
  <div id="taking-longer" role="alert" hidden>
    <p>Your payment is taking longer than usual to confirm. This can take a few minutes.</p>
    <p>Once it is confirmed, your voting link will be on the <a href="/site-selection">site selection page</a>.</p>
    <p>If you were charged and the link still isn't there after a while, contact <a href="mailto:<?php echo $support_email; ?>"><?php echo $support_email; ?></a>.</p>
  </div>
</article>
<script>
(function() {
  "use strict";

  const maxAttempts = 20;
  let attempts = 0;

  function giveUp() {
    document.getElementById('taking-longer').hidden = false;
  }

  function check() {
    attempts++;
    fetch('/site-selection/status', {credentials: 'same-origin'})
      .then(response => response.json())
      .then(data => {
        if (data.has_token) {
          window.location.href = '/site-selection?success=true';
        } else if (attempts >= maxAttempts) {
          giveUp();
        } else {
          setTimeout(check, 2000);
        }
      })
      .catch(() => {
        if (attempts >= maxAttempts) {
          giveUp();
        } else {
          setTimeout(check, 2000);
        }
      });
  }

  check();
})();
</script>
<?php
render_footer();
?>
