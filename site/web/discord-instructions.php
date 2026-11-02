<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

render_page('pages/discord-instructions.html.twig', [
  'title' => 'Discord instructions',
  'description' => 'Instructions on how to join the ' . CON_SHORT_NAME . ' Discord server.',
  'breadcrumbs' => ['Home' => '/', 'Chat online' => '/chat', 'How to join the Discord Server'],
]);
