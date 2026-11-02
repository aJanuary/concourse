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
  $start = $_POST['start'] ?: null;
  $duration = $_POST['duration'] ?: null;
  $room_id = $_POST['room_id'] ?: null;
  $post_url = $_POST['post_url'];

  if ($action == "add") {
    try {
      db_add_discord_post($item_id, $title, $start, $duration, $room_id, $post_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/discord-posts/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
  } else if ($action == "delete") {
    try {
      db_delete_discord_post($item_id);
    } catch (Exception $e) {
      header('Location: /admin/programme/discord-posts/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
  } else if ($action == "edit") {
    try {
      db_edit_discord_post($item_id, $title, $start, $duration, $room_id, $post_url);
    } catch (Exception $e) {
      header('Location: /admin/programme/discord-posts/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
  }
  header('Location: /admin/programme/discord-posts/list');
  exit;
}

// Null when adding
$item_id = $_GET['item_id'] ?? null;
if ($item_id !== null) {
  $post = db_get_discord_post($item_id);
  $heading = 'Edit ' . $item_id;
} else {
  $post = ['title' => '', 'start' => '', 'duration' => '', 'room_id' => '', 'post_url' => ''];
  $heading = 'Add Discord post';
}

render_page('pages/admin/programme/discord-posts/edit.html.twig', [
  'heading' => $heading,
  'editing' => $item_id !== null,
  'item_id' => $item_id,
  'post' => $post,
]);
