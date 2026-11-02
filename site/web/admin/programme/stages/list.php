<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
$items = [];
foreach (db_get_stages() as $row) {
  $items[] = [
    'label' => $row['title'] ?: $row['room_id'],
    'url' => '/admin/programme/stages/edit?room_id=' . urlencode($row['room_id']),
    'subtitle' => $row['title'] ? $row['room_id'] : null,
  ];
}

render_page('pages/admin/programme/stages/list.html.twig', [
  'error' => $_GET['error'] ?? null,
  'items' => $items,
]);
