<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/conclar.php');
require_once(getenv('CONFIG_LIB_DIR') . '/csv.php');

check_permission('see-programme-reports');

$app_id = $_GET['app_id'] ?? (CONCLAR_VALID_APP_IDS[0] ?? '');
if (!is_string($app_id) || ($app_id !== '' && !conclar_validate_app_id($app_id))) {
  render_404();
}

$counts = $app_id === '' ? [] : conclar_get_favourite_counts($app_id);
$titles = $counts ? conclar_fetch_item_titles() : [];

if ($counts && ($_GET['download'] ?? '') === 'csv') {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="programme-favourites-' . date('Y-m-d') . '.csv"');

  $output = fopen('php://output', 'w');
  fputcsv($output, ['Item ID', 'Title', 'Favourites', 'Online', 'In person']);
  foreach ($counts as $row) {
    fputcsv($output, [
      csv_safe_cell($row['item_id']),
      csv_safe_cell($titles[$row['item_id']] ?? ''),
      $row['total'],
      $row['online'],
      $row['in_person'],
    ]);
  }
  fclose($output);
  exit;
}

$rows = [];
foreach ($counts as $row) {
  $rows[] = [
    'title' => $titles[$row['item_id']] ?? $row['item_id'],
    'total' => (int) $row['total'],
    'online' => (int) $row['online'],
    'in_person' => (int) $row['in_person'],
  ];
}

render_page('pages/admin/programme-reports/favourites.html.twig', [
  'app_id' => $app_id,
  'rows' => $rows,
  'member_count' => $counts ? conclar_count_members_with_favourites($app_id) : 0,
]);
