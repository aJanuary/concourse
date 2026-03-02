<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?: null;
  $room_id = $_POST['room_id'] ?: null;
  $title = $_POST['title'] ?: null;
  $url = $_POST['url'] ?: null;

  if ($action == "add") {
    try {
      db_add_stream_url($room_id, $title, $url);
    } catch (Exception $e) {
      header('Location: /admin/programme/stream-urls/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "edit") {
    try {
      db_edit_stream_url($room_id, $title, $url);
    } catch (Exception $e) {
      header('Location: /admin/programme/stream-urls/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "delete") {
    try {
      db_delete_stream_url($room_id);
    } catch (Exception $e) {
      header('Location: /admin/programme/stream-urls/list?error=' . $e->getMessage());
      throw $e;
    }
  }
  header('Location: /admin/programme/stream-urls/list');
  exit;
}

if (array_key_exists('room_id', $_GET)) {
  $room_id = $_GET['room_id'];
  $stream_url = db_get_stream_url($room_id);
}

if (array_key_exists('room_id', $_GET)) {
  $title = 'Edit ' . $room_id;
} else {
  $title = 'Add stream URL';
}

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
      <fieldset>
        <legend>Stream URL details</legend>
      <?php
if (array_key_exists('room_id', $_GET)) {
?>
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
        <input type="hidden" name="action" value="edit">
<?php
} else {
?>
        <input type="hidden" name="action" value="add">
        <p><label>Room ID: <input name="room_id" required></label></p>
<?php
}
?>
        <p><label>Title: <input name="title" maxlength="256" value="<?php echo htmlspecialchars($stream_url['title']); ?>"></label></p>
        <p><label>URL: <input type="text" name="url" value="<?php echo htmlspecialchars($stream_url['url']); ?>"></label></p>
      </fieldset>
      <input type="submit" value="Save">
<?php
if (array_key_exists('room_id', $_GET)) {
?>
      <input id="delete" type="button" value="Delete">
<?php
}
?>
    </form>

    <script>
      var $delete = document.getElementById('delete');
      if ($delete) {
        $delete.addEventListener('click', function(e) {
          if (confirm('Are you sure you want to delete <?php echo $room_id; ?>?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/programme/stream-urls/edit';
            var roomId = '<?php echo $room_id; ?>';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action';
            input.value = 'delete';
            form.appendChild(input);
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'room_id';
            input.value = roomId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
          }
        });
      }
    </script>
    <article>
  <?php
  render_footer();
  ?>
