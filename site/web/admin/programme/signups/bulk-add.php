<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/signups.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

$csv = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $csv = (string) ($_POST['csv'] ?? '');
  [$signups, $errors] = signups_from_csv($csv);

  if (!$errors) {
    try {
      db_add_signups($signups);
    } catch (Exception $e) {
      header('Location: /admin/programme/signups/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
    header('Location: /admin/programme/signups/list');
    exit;
  }
}

render_page('pages/admin/programme/signups/bulk-add.html.twig', [
  'errors' => $errors,
  'csv' => $csv,
  'timezone' => TIMEZONE,
]);
