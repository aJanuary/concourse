<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = 'always';
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

render_page('pages/privacy-policy.html.twig', [
  'title' => 'Privacy policy',
  'description' => 'An explanation of the cookies we set and what they are used for',
  'breadcrumbs' => ['Home' => '/', 'Privacy policy'],
]);
