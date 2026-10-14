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
  $title = $_POST['title'] ?: null;
  $replay_url = $_POST['replay_url'] ?? '';
  $unavailable = isset($_POST['unavailable']);

  if ($action == "add") {
    try {
      db_add_replay($item_id, $title, $replay_url, $unavailable);
    } catch (Exception $e) {
      header('Location: /admin/programme/replay/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "delete") {
    try {
      db_delete_replay($item_id);
    } catch (Exception $e) {
      header('Location: /admin/programme/replay/list?error=' . $e->getMessage());
      throw $e;
    }
  } else if ($action == "edit") {
    try {
      db_edit_replay($item_id, $title, $replay_url, $unavailable);
    } catch (Exception $e) {
      header('Location: /admin/programme/replay/list?error=' . $e->getMessage());
      throw $e;
    }
  }
  header('Location: /admin/programme/replay/list');
  exit;
}

$item_id = $_GET['item_id'] ?? '';
$replay = db_get_replay($item_id);
// A replay that doesn't exist yet can still be filled in from the link on the session page
$new_replay = !$replay['found'];
$replay['title'] = $replay['title'] ?? $_GET['title'] ?? '';
$replay['replay_url'] = $replay['replay_url'] ?? $_GET['replay_url'] ?? '';

if ($item_id !== '' && !$new_replay) {
  $title = 'Edit ' . $item_id;
} else {
  $title = 'Add replay';
}

render_header(
  'Add/edit a replay',
  'Manage replays.',
  [
    'Home' => '/',
    'Manage programme' => '/admin/programme/list',
    'Manage replays' => '/admin/programme/replay/list',
    htmlspecialchars($title)
  ]
);
?>
  <article>
    <h2><?php echo htmlspecialchars($title); ?></h2>
    <form action="/admin/programme/replay/edit" method="POST" class="vertical">
      <?php echo csrf_field(); ?>
      <fieldset>
        <legend>Replay details</legend>
      <?php
if (!$new_replay) {
?>
        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
        <input type="hidden" name="action" value="edit">
<?php
} else {
?>
        <input type="hidden" name="action" value="add">
        <p><label>Plano Item ID: <input name="item_id" value="<?php echo htmlspecialchars($item_id); ?>" required pattern="^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$"></label></p>
<?php
}
?>
        <p><label>Title: <input name="title" maxlength="256" value="<?php echo htmlspecialchars($replay['title']); ?>"></label></p>
        <p><label>Replay URL: <input type="text" name="replay_url" value="<?php echo htmlspecialchars($replay['replay_url']); ?>"></label></p>
        <p><label><input type="checkbox" name="unavailable"<?php echo $replay['unavailable'] ? ' checked' : ''; ?>> No replay will be available (shows a message instead of the replay)</label></p>
      </fieldset>
      <input type="submit" value="Save">
      <?php
if (!$new_replay) {
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
            form.action = '/admin/programme/replay/edit';
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
    <article>
  <?php
  render_footer();
  ?>