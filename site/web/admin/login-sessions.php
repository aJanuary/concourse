<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (!current_user_has_permission('manage-login-sessions')) {
  header('Location: /');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($_POST as $session_id => $value) {
    if ($value === 'on') {
      db_delete_session($session_id);
    }
  }
  header('Location: /admin/login-sessions');
  exit;
}
render_page('pages/admin/login-sessions.html.twig', [
  'title' => 'Manage login sessions',
  'description' => 'Manage login sessions.',
  'breadcrumbs' => ['Home' => '/', 'Manage login sessions'],
  'sessions' => db_get_sessions(),
]);
