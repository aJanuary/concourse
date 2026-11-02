<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

check_permission('see-participant-guides');

render_page('pages/guides/list.html.twig', [
  'title' => 'Participant guides',
  'description' => 'Participant guides for ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Guides'],
]);
