<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;
  $room_id = $_POST['room_id'] ?: null;
  $title = $_POST['title'] ?: null;
  $url = $_POST['url'] ?: null;

  try {
    if ($action == 'add') {
      db_add_stream_url($room_id, $title, $url);
    } else if ($action == 'edit') {
      db_edit_stream_url($room_id, $title, $url);
    } else if ($action == 'delete') {
      db_delete_stream_url($room_id);
    }
  } catch (Exception $e) {
    header('Location: /admin/programme/stream-urls/list?error=' . urlencode($e->getMessage()));
    throw $e;
  }
  header('Location: /admin/programme/stream-urls/list');
  exit;
}

$room_id = $_GET['room_id'] ?? null;
$stream_url = $room_id !== null ? db_get_stream_url($room_id) : ['title' => '', 'url' => ''];
$editing = $room_id !== null;
$title = $editing ? 'Edit ' . $room_id : 'Add stream URL';

render_header(
  'Add/edit a stream URL',
  'Manage stream URLs.',
  [
    'Home' => '/',
    'Manage programme' => '/admin/programme/list',
    'Manage stream URLs' => '/admin/programme/stream-urls/list',
    htmlspecialchars($title)
  ]
);
?>
  <article>
    <h2><?php echo htmlspecialchars($title); ?></h2>
    <form action="/admin/programme/stream-urls/edit" method="POST" class="vertical">
      <?php echo csrf_field(); ?>
      <fieldset>
        <legend>Stream URL details</legend>
<?php
if ($editing) {
?>
        <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room_id); ?>">
        <input type="hidden" name="action" value="edit">
<?php
} else {
?>
        <input type="hidden" name="action" value="add">
        <p><label>Room ID: <input name="room_id" maxlength="36" required></label></p>
<?php
}
?>
        <p><label>Title: <input name="title" maxlength="256" value="<?php echo htmlspecialchars($stream_url['title'] ?? ''); ?>"></label></p>
        <p><label>URL (leave empty when the room is not streaming): <input type="text" name="url" value="<?php echo htmlspecialchars($stream_url['url'] ?? ''); ?>"></label></p>
      </fieldset>
      <input type="submit" value="Save">
<?php
if ($editing) {
?>
      <input id="delete" type="button" value="Delete">
<?php
}
?>
    </form>
<?php
if ($editing) {
?>
    <form id="delete-form" action="/admin/programme/stream-urls/edit" method="POST" hidden>
      <?php echo csrf_field(); ?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room_id); ?>">
    </form>
    <script>
      document.getElementById('delete').addEventListener('click', function() {
        if (confirm('Are you sure you want to delete ' + <?php echo json_encode($room_id, JSON_HEX_TAG | JSON_HEX_AMP); ?> + '?')) {
          document.getElementById('delete-form').submit();
        }
      });
    </script>
<?php
}
?>
  </article>
<?php
render_footer();
?>
