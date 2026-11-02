<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

render_page('pages/upload-presentation.html.twig', [
  'title' => 'Upload presentation',
  'description' => 'Upload a presentation for a programme item at ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Upload presentation'],
]);
