<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

render_page('pages/admin/programme/list.html.twig', [
  'title' => 'Manage programme',
  'description' => 'Manage programme.',
  'breadcrumbs' => ['Home' => '/', 'Manage programme'],
]);
