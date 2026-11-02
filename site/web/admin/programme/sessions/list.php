<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
$items = [];
foreach (db_get_prog_sessions() as $row) {
  $items[] = [
    'label' => $row['title'] ?: $row['item_id'],
    'url' => '/admin/programme/sessions/edit?item_id=' . urlencode($row['item_id']),
    'subtitle' => $row['title'] ? $row['item_id'] : null,
  ];
}

render_page('pages/admin/programme/sessions/list.html.twig', [
  'items' => $items,
]);
