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
  $room_id = $_POST['room_id'] ?: null;
  $title = $_POST['title'] ?: null;
  $viewer_url = $_POST['viewer_url'] ?: null;
  $type = $_POST['type'] ?: null;
  $participant_url = $_POST['participant_url'] ?: null;

  if ($action == "add") {
    try {
      db_add_stage($room_id, $title, $viewer_url, $type, $participant_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/stages/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
  } else if ($action == "edit") {
    try {
      db_edit_stage($room_id, $title, $viewer_url, $type, $participant_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/stages/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
  } else if ($action == "delete") {
    try {
      db_delete_stage($room_id);
    } catch (Exception $e) {
      header('Location: /admin/programme/stages/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
  }
  header('Location: /admin/programme/stages/list');
  exit;
}

// Null when adding
$room_id = $_GET['room_id'] ?? null;
if ($room_id !== null) {
  $stage = db_get_stage($room_id);
} else {
  $stage = ['title' => '', 'viewer_url' => '', 'type' => '', 'participant_url' => ''];
}

render_page('pages/admin/programme/stages/edit.html.twig', [
  'editing' => $room_id !== null,
  'room_id' => $room_id,
  'stage' => $stage,
]);
