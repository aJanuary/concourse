<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/rce.php');

if (array_key_exists('invite', $_GET)) {
  check_permission('see-rce-link');

  $magic_link = get_magic_rce_link($_COOKIE['session']);
  header('Location: ' . $magic_link);
  setcookie('seen-rce-invite', '1', time() + 60*60*24*365, '/');
  exit;
}

render_header(
  'Stream',
  'Watch stream and replays of programme items at Glasgow 2024, A Worldcon for Our Futures through RingCentral Events.',
  ['Home' => '/', 'Stream and replay']
);
?>
<article>
  <h2>Stream and replay</h2>
  <p>We are using a platform called RingCentral Events to stream our programme items. You can watch the live streams of programme items, or watch the recorded videos afterwards. It works on both desktop and mobile.</p>
  <p>Items will be available for catch-up until the end of 2024.</p>
<?php
  if (is_anonymous()) {
?>
  <p><a class="button" href="<?php echo make_login_link(); ?>">Log in to watch</a></p>
<?php
  } else if (current_user_has_permission('hall-pass')) {
?>
  <p>Access to RingCentral Events is not available with the Hall Pass ticket.</p>
<?php
  } else if (!current_user_has_permission('see-rce-link')) {
?>
  <p>Our RingCentral Event will open Wednesday morning. Please check back then.</p>
<?php
  } else {
?>
  <p><a class="button" target="_blank" href="?invite">Go to RingCentral Events</a></p>
<?php
  }
?>
  <h3>How to use RingCentral Events</h3>
  <p>We are working on updating our guide to RingCentral Events. In the meantime, you can check out the <a href="https://eastercon2024.co.uk/guide-to-ringcentral-events/">guide from Levitation 2024</a> which contains:</p>
  <ul>
    <li>Watch a live stream</li>
    <li>Watch catch up items</li>
    <li>Turn on captions</li>
    <li>Use Picture in Picture</li>
    <li>AirPlay and Cast</li>
  </ul>
</article>

<?php
render_footer();
?>
