<?php
// Deliberately don't allow implicit anonymous access.
// This can redirect straight to the zoom URL and the stream yard URLs.
// While we have people in those spaces to make sure the right people are there,
// if we allow anonymous access there is potential for people to join en masse
// and cause disruption.
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/deep_link.php');

if (array_key_exists('item_id', $_GET)) {
  $type = 'session';
  $item_id = $_GET['item_id'];
  $session = db_get_prog_session($item_id);
  $participant_url = $session['rce_url'];
} else if (array_key_exists('room_id', $_GET)) {
  $room_id = $_GET['room_id'];
  $stage = db_get_stage($room_id);
  if ($stage['type'] == 'hybrid') {
    $type = 'zoom';
    $participant_url = db_get_zoom_url();
  } else {
    $type = 'stream yard';
    $participant_url = $stage['participant_url'];
  }
} else {
  die('Missing required query parameter "item_id" or "room_id"');
}

if (is_anonymous()) {
  render_header("Log in to participate.");
?>
  <a href="/" class="back">&lt; Back to member portal</a>

  <article>
    <h3>Log in to participate</h3>
    <p>You need to be logged in to participate in this item. Please log in to the members portal to continue.</p>
    <p><a href="<?php echo make_login_link(); ?>" class="button">Log in</a></p>
  </article>
<?php
  render_footer();
  exit;
}

if (!$participant_url) {
  render_header("Unknown item.");
  ?>
  <a href="/" class="back">&lt; Back to member portal</a>

  <article>
    <h3>Unknown item</h3>
    <p>Oops, something wen&apos;t wrong and we don&apos;t know where you should be going. Please get in touch with the programme team to find out. TODO: How?</p>
  </article>
  <?php
  render_footer();
  exit;
}

if ($type == 'zoom' || array_key_exists('seen-rce-invite', $_COOKIE)) {
  header('Location: ' . $participant_url);
  exit;
}

render_rce_wizard($participant_url);
?>