<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
$items = [];
foreach (db_get_discord_posts() as $row) {
  $items[] = [
    'label' => $row['title'] ?: $row['item_id'],
    'url' => '/admin/programme/discord-posts/edit?item_id=' . urlencode($row['item_id']),
    'subtitle' => $row['title'] ? $row['item_id'] : null,
  ];
}

render_page('pages/admin/programme/discord-posts/list.html.twig', [
  'error' => $_GET['error'] ?? null,
  'items' => $items,
]);
