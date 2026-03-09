<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Stream',
  'Watch stream and replays of programme items at ' . CON_NAME . '.',
  ['Home' => '/', 'Stream and replay']
);
?>
<article>
  <h2>Stream and replay</h2>
  <h3>Live streams</h3>
<?php
  if (is_anonymous()) {
?>
  <p><a class="button" href="<?php echo make_login_link(); ?>">Log in to watch</a></p>
<?php
  } else {
    $stream_urls = db_get_stream_urls();
?>
  <ul>
<?php
    foreach ($stream_urls as $stream_url) {
      $title = $stream_url['title'] ?: $stream_url['room_id'];
      $url = $stream_url['url'];
?>
    <?php if ($url) { ?>
      <li><a target="_blank" href="<?php echo htmlspecialchars($url); ?>"><?php echo htmlspecialchars($title); ?></a></li>
    <?php } else { ?>
      <li class="no-stream"><?php echo htmlspecialchars($title); ?><br>This room is not currently streaming</li>
    <?php } ?>
<?php
    }
?>
  </ul>
<?php
  }
?>
  <h3>Replay</h3>
  <p>To access the replay, find the item in our <a href="https://guide.eastercon2026.org">programme guide</a> and click the "Watch replay" link in the item description.</p>
  <p>Items will be available for catch-up until the end of the year.</p>
</article>

<?php
render_footer();
?>
