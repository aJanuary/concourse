<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

render_page('pages/attributions.html.twig', [
  'title' => 'Icon attributions',
  'description' => 'Attributions for the icons used on the site.',
  'breadcrumbs' => ['Home' => '/', 'Icon attributions'],
]);
