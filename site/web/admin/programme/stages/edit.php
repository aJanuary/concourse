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
  $viewer_url = $_POST['viewer_url'] ?: null;
  $type = $_POST['type'] ?: null;
  $participant_url = $_POST['participant_url'] ?: null;

  if ($action == "add") {
    try {
      db_add_stage($room_id, $viewer_url, $type, $participant_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/stages/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "edit") {
    try {
      db_edit_stage($room_id, $viewer_url, $type, $participant_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/stages/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "delete") {
    try {
      db_delete_stage($room_id);
    } catch (Exception $e) {
      header('Location: /admin/programme/stages/list?error=' . $e->getMessage());
      throw $e;
    }
  }
  header('Location: /admin/programme/stages/list');
  exit;
}

if (array_key_exists('room_id', $_GET)) {
  $room_id = $_GET['room_id'];
  $stage = db_get_stage($room_id);
}

render_header("Manage RCE stages.");
?>
  
  <a href="/admin/programme/stages/list" class="back">&lt; Back to List RCE stages</a>
  
  <article>
<?php
if (array_key_exists('room_id', $_GET)) {
?>
    <h2>Edit <?php echo $room_id; ?></h2>
<?php
} else {
?>
    <h2>Add stage</h2>
<?php
}
?>
    <form action="/admin/programme/stages/edit" method="POST" class="vertical">
      <fieldset>
        <legend>Stage details</legend>
      <?php
if (array_key_exists('room_id', $_GET)) {
?>
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
        <input type="hidden" name="action" value="edit">
<?php
} else {
?>
        <input type="hidden" name="action" value="add">
        <p><label>Plano Room ID: <input name="room_id" required pattern="^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$"></label></p>
<?php
}
?>
        <p><label>Viewer URL: <input name="viewer_url" value="<?php echo $stage['viewer_url']; ?>"></label></p>
        <p><label>Type: <select name="type">
          <option value="hybrid" <?php if ($stage['type'] == 'hybrid') { echo 'selected'; } ?>>Hybrid</option>
          <option value="online-only" <?php if ($stage['type'] == 'online-only') { echo 'selected'; } ?>>Online-only</option>
        </select></label></p>
        <p><label>Stream Yard URL: <input name="participant_url" value="<?php echo $stage['participant_url']; ?>"></label></p>
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
      var $type = document.querySelector('select[name="type"]');
      var $participantUrl = document.querySelector('input[name="participant_url"]');
      function handleTypeChange() {
        $participantUrl.disabled = $type.value === 'hybrid';
      }
      $type.addEventListener('change', handleTypeChange);
      handleTypeChange();

      var $delete = document.getElementById('delete');
      if ($delete) {
        $delete.addEventListener('click', function(e) {
          if (confirm('Are you sure you want to delete <?php echo $stage['rpom_id']; ?>?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/programme/stages/edit';
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
  <?php
  render_footer();
  ?>