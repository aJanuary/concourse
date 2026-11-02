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
  $destination = $_POST['destination'] ?? '';
  $handler = $_POST['handler'] ?: null;
  if ($handler !== null && !in_array($handler, AVAILABLE_PROG_HANDLERS, true)) {
    header('Location: /admin/programme/sessions/list?error=' . urlencode('Unknown handler'));
    exit;
  }

  if ($action == "add") {
    try {
      db_add_prog_session($item_id, $title, $destination, $handler);
    } catch (Exception $e) {
      header('Location: /admin/programme/sessions/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
  } else if ($action == "delete") {
    try {
      db_delete_prog_session($item_id);
    } catch (Exception $e) {
      header('Location: /admin/programme/sessions/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
  } else if ($action == "edit") {
    try {
      db_edit_prog_session($item_id, $title, $destination, $handler);
    } catch (Exception $e) {
      header('Location: /admin/programme/sessions/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
  }
  header('Location: /admin/programme/sessions/list');
  exit;
}

// Null when adding
$item_id = $_GET['item_id'] ?? null;
if ($item_id !== null) {
  $session = db_get_prog_session($item_id);
  $heading = 'Edit ' . $item_id;
} else {
  $session = ['title' => '', 'destination' => '', 'handler' => null];
  $heading = 'Add session';
}
$same_replay_destination = ($session['handler'] ?? DEFAULT_PROG_HANDLER) === PROG_REPLAY_HANDLER;

render_page('pages/admin/programme/sessions/edit.html.twig', [
  'heading' => $heading,
  'item_id' => $item_id,
  'session' => $session,
  'default_handler' => DEFAULT_PROG_HANDLER,
  'handlers' => AVAILABLE_PROG_HANDLERS,
  'replay_query' => $same_replay_destination
    ? ['item_id' => $item_id, 'title' => $session['title'], 'replay_url' => $session['destination']]
    : ['item_id' => $item_id, 'title' => $session['title']],
]);
