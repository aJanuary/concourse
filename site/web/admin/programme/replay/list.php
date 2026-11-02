<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
$items = [];
foreach (db_get_replays() as $row) {
  $items[] = [
    'label' => $row['title'] ?: $row['item_id'],
    'url' => '/admin/programme/replay/edit?item_id=' . urlencode($row['item_id']),
    'subtitle' => $row['title'] ? $row['item_id'] : null,
  ];
}

render_page('pages/admin/programme/replay/list.html.twig', [
  'items' => $items,
]);
