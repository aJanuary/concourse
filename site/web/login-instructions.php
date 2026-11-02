<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = 'always';
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

render_page('pages/login-instructions.html.twig', [
  'title' => 'Log in instructions',
  'description' => 'Instructions on how to log in to the ' . CON_SHORT_NAME . ' member portal.',
  'breadcrumbs' => ['Home' => '/', 'How to log in'],
]);
