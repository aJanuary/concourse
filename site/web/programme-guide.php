<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (current_user_has_permission('see-guide-redirect')) {
  header('Location: ' . 'https://example.com/programme-guide');
  exit;
}

render_page('pages/programme-guide.html.twig', [
  'title' => 'Programme guide',
  'description' => 'Programme guide for ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Programme guide'],
]);
