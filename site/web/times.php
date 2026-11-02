<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

render_page('pages/times.html.twig', [
  'title' => 'Opening times',
  'description' => 'Opening times for ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Opening times'],
]);
