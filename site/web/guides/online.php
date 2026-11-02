<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

check_permission('see-participant-guides');

render_page('pages/guides/online.html.twig', [
  'title' => 'Online participant guide',
  'description' => 'Online participant guide for ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Guides' => '/guides/list', 'Online participant guide'],
]);
