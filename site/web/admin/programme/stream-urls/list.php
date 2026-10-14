<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

render_header(
  'Manage stream URLs',
  'Manage stream URLs.',
  ['Home' => '/', 'Manage programme' => '/admin/programme/list', 'Manage stream URLs']
);
?>
  <article>
    <h2>Manage stream URLs</h2>
<?php
if (isset($_GET['error'])) {
?>
    <p class="error"><img src="/resources/error.svg" alt="error" aria-label="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php
}
?>
    <ul>
      <li><a href="/admin/programme/stream-urls/edit">Add stream URL...</a></li>
<?php
foreach (db_get_stream_urls() as $stream_url) {
  $label = $stream_url['title'] ?: $stream_url['room_id'];
?>
      <li>
        <a aria-label="Edit stream URL <?php echo htmlspecialchars($label); ?>" href="/admin/programme/stream-urls/edit?room_id=<?php echo urlencode($stream_url['room_id']); ?>"><?php echo htmlspecialchars($label); ?></a>
<?php
  if ($stream_url['title']) {
    echo '<br>' . htmlspecialchars($stream_url['room_id']);
  }
?>
      </li>
<?php
}
?>
    </ul>
  </article>
<?php
render_footer();
?>
