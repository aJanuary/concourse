<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

render_page('pages/help.html.twig', [
  'title' => 'Help',
  'description' => 'How to get help at ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Help'],
]);
