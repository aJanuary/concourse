<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/signups.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

$items = [];
foreach (signups_sort(db_get_signups()) as $signup) {
  $items[] = [
    'label' => $signup['title'],
    'url' => '/admin/programme/signups/edit?id=' . urlencode((string) $signup['id']),
    'subtitle' => $signup['category'] . ($signup['session_start'] !== null ? ', ' . signup_format_time($signup['session_start']) : ''),
  ];
}

render_page('pages/admin/programme/signups/list.html.twig', [
  'title' => 'Manage sign-ups',
  'description' => 'Manage sign-ups.',
  'breadcrumbs' => ['Home' => '/', 'Manage programme' => '/admin/programme/list', 'Manage sign-ups'],
  'error' => $_GET['error'] ?? null,
  'items' => $items,
]);
