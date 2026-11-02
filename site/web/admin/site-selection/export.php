<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');
require_once(getenv('CONFIG_LIB_DIR') . '/countries.php');
require_once(getenv('CONFIG_LIB_DIR') . '/csv.php');

// The details of everyone who paid online, for the winning bid
check_permission('manage-tokens');

$rows = db_get_site_selection_export();

if (($_GET['download'] ?? '') === 'csv') {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="site-selection-members-' . date('Y-m-d') . '.csv"');
  $yes_no = fn($value) => $value === null ? '' : ($value ? 'Yes' : 'No');
  $output = fopen('php://output', 'w');
  fputcsv($output, [
    CON_SHORT_NAME . ' badge number',
    CON_SHORT_NAME . ' badge name',
    'Name',
    'Email',
    'Country code',
    'Country',
    'Address',
    'Consents to publishing name',
    'Wants paper publications',
  ], ',', '"', '');
  foreach ($rows as $row) {
    fputcsv($output, [
      csv_safe_cell($row['badge_no']),
      csv_safe_cell($row['name']),
      csv_safe_cell($row['real_name']),
      csv_safe_cell($row['email']),
      csv_safe_cell($row['country']),
      csv_safe_cell(COUNTRIES[$row['country']] ?? ''),
      csv_safe_cell($row['address']),
      $yes_no($row['consent_to_publish_name']),
      $yes_no($row['wants_paper_publications']),
    ], ',', '"', '');
  }
  fclose($output);
  exit;
}

render_page('pages/admin/site-selection/export.html.twig', [
  'count' => count($rows),
]);
