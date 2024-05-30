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
  $item_id = $_POST['item_id'] ?: null;
  $start = $_POST['start'] ?: null;
  $duration = $_POST['duration'] ?: null;
  $room_id = $_POST['room_id'] ?: null;
  $post_url = $_POST['post_url'];

  if ($action == "add") {
    try {
      db_add_discord_post($item_id, $start, $duration, $room_id, $post_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/discord-posts/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "delete") {
    try {
      db_delete_discord_post($item_id);
    } catch (Exception $e) {
      header('Location: /admin/programme/discord-posts/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "edit") {
    try {
      db_edit_discord_post($item_id, $start, $duration, $room_id, $post_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/discord-posts/list?error=' . $e->getMessage());
      throw $e;
    }
  }
  header('Location: /admin/programme/discord-posts/list');
  exit;
}

if (array_key_exists('item_id', $_GET)) {
  $item_id = $_GET['item_id'];
  $post = db_get_discord_post($item_id);
}

render_header();
?>
  
  <a href="/admin/programme/discord-posts/list" class="back">&lt; Back to List Discord posts</a>
  
  <article>
<?php
if (array_key_exists('item_id', $_GET)) {
?>
    <h3>Edit <?php echo $item_id; ?></h3>
<?php
} else {
?>
    <h3>Add Discord post</h3>
<?php
}
?>
    <p>Warning: These may be overriden by Watson</p>
    <form action="/admin/programme/discord-posts/edit" method="POST" class="vertical">
      <fieldset>
      <?php
if (array_key_exists('item_id', $_GET)) {
?>
        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
        <input type="hidden" name="action" value="edit">
<?php
} else {
?>
        <input type="hidden" name="action" value="add">
        <p><label>Plano Item ID: <input name="item_id" required pattern="^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$"></label></p>
<?php
}
?>
        <p><label>Start date/time (yyyy-mm-dd hh:mm in 24h BST): <input name="start" pattern="^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$" value="<?php echo $post['start']; ?>"></label></p>
        <p><label>Duration (mins): <input name="duration" value="<?php echo $post['duration']; ?>" pattern="^\d*$"></label></p>
        <p><label>Plano Room ID: <input name="room_id" value="<?php echo $post['room_id']; ?>" required pattern="^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$"></label></p>
        <p><label>Discord URL: <input type="text" name="post_url" value="<?php echo $post['post_url']; ?>"></label></p>
      </fieldset>
      <input type="submit" value="Save">
      <?php
if (array_key_exists('item_id', $_GET)) {
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
          if (confirm('Are you sure you want to delete <?php echo $item_id; ?>?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/programme/discord-posts/edit';
            var itemId = '<?php echo $item_id; ?>';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'action';
            input.value = 'delete';
            form.appendChild(input);
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'item_id';
            input.value = itemId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
          }
        });
      }
    </script>
  <?php
  render_footer();
  ?>