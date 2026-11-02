<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

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
  $heading = 'Edit ' . $item_id;
} else {
  $heading = 'Add replay';
}

render_page('pages/admin/programme/replay/edit.html.twig', [
  'heading' => $heading,
  'item_id' => $item_id,
  'new_replay' => $new_replay,
  'replay' => $replay,
]);
