<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

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

render_page('pages/admin/programme/stream-urls/edit.html.twig', [
  'title' => 'Add/edit a stream URL',
  'description' => 'Manage stream URLs.',
  'breadcrumbs' => [
    'Home' => '/',
    'Manage programme' => '/admin/programme/list',
    'Manage stream URLs' => '/admin/programme/stream-urls/list',
    $title,
  ],
  'heading' => $title,
  'room_id' => $room_id,
  'editing' => $editing,
  'stream_url' => $stream_url,
]);
