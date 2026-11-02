<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

render_page('pages/current-user.html.twig', [
  'title' => 'Current user',
  'description' => 'Who is logged in to ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Current user'],
  'name' => get_current_user_name(),
]);
