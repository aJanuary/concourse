<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

$page = $_GET['page'] ?? '';

render_page('pages/under-construction.html.twig', [
  'title' => 'Under constructions',
  'description' => 'Under construction.',
  'breadcrumbs' => ['Home' => '/', $page],
  'page' => $page,
]);
