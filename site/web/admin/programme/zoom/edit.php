<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $zoom_url = $_POST['zoom_url'] ?: '';

  try {
    db_set_zoom_url($zoom_url);
  } catch (Exception $e) {
    header('Location: /admin/programme/zoom/edit?error=' . $e->getMessage());
    throw $e;
  }

  header('Location: /admin/programme/list');
  exit;
}

$zoom_url = db_get_zoom_url();

render_page('pages/admin/programme/zoom/edit.html.twig', [
  'zoom_url' => $zoom_url,
]);
