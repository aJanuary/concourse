<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

render_page('pages/maps.html.twig', [
  'title' => 'Maps',
  'description' => 'Maps of the various venues at ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Maps'],
]);
