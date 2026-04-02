<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!array_key_exists('room_id', $_GET)) {
  die('Missing required query parameter "room_id"');
}

$room_id = $_GET['room_id'];

if (is_anonymous()) {
  render_header(
    'View stream',
    'Log in to view stream.',
    ['Home' => '/', 'View stream']
  );
?>
  <article>
    <h2>Log in to view stream</h2>
    <p>You need to be logged in to view this stream. Please log in to the members portal to continue.</p>
    <p><a href="<?php echo make_login_link(); ?>" class="button">Log in</a></p>
  </article>
<?php
  render_footer();
  exit;
}

$stream = db_get_stream_url($room_id);

if (!$stream['found']) {
  render_header(
    'View stream',
    'Unknown room.',
    ['Home' => '/', 'View stream']
  );
?>
  <article>
    <h2>Unknown room</h2>
    <p>Oops, something went wrong and we don&apos;t know where you should be going. Please get in touch with the programme team to find out.</p>
  </article>
<?php
  render_footer();
  exit;
}

if (!$stream['url']) {
  render_header(
    'View stream',
    'Room not streaming.',
    ['Home' => '/', 'View stream']
  );
?>
  <article>
    <h2>Room not streaming</h2>
    <p>This room is not currently streaming. Please check back later.</p>
  </article>
<?php
  render_footer();
  exit;
}

header('Location: ' . $stream['url']);
exit;
?>
