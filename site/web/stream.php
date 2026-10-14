<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Stream',
  'Watch streams and replays of programme items at ' . CON_NAME . '.',
  ['Home' => '/', 'Stream and replay']
);
?>
<article>
  <h2>Stream and replay</h2>
  <h3>Live streams</h3>
<?php
  if (!is_logged_in()) {
?>
  <p><a class="button" href="<?php echo htmlspecialchars(make_login_link()); ?>">Log in to watch</a></p>
<?php
  } else if (PROG_JOIN_PERMISSION !== '' && !current_user_has_permission(PROG_JOIN_PERMISSION)) {
?>
  <p>Your membership doesn't include access to the streams.</p>
<?php
  } else {
?>
  <ul>
<?php
    foreach (db_get_stream_urls() as $stream_url) {
      $title = $stream_url['title'] ?: $stream_url['room_id'];
      if ($stream_url['url']) {
?>
    <li><a target="_blank" href="/deep-link/stream?room_id=<?php echo urlencode($stream_url['room_id']); ?>"><?php echo htmlspecialchars($title); ?></a></li>
<?php
      } else {
?>
    <li class="no-stream"><?php echo htmlspecialchars($title); ?><br>This room is not currently streaming</li>
<?php
      }
    }
?>
  </ul>
<?php
  }
?>
  <h3>Replay</h3>
  <p>To watch a replay, find the item in the <a href="/programme-guide">programme guide</a> and follow its replay link.</p>
</article>

<?php
render_footer();
?>
