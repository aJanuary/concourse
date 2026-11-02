<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

check_permission('see-participant-guides');

render_page('pages/guides/moderator.html.twig', [
  'title' => 'Moderator guide',
  'description' => 'Moderator guide for ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Guides' => '/guides/list', 'Moderator guide'],
]);
