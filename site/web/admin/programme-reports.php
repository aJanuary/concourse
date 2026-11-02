<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

check_permission('see-programme-reports');

render_page('pages/admin/programme-reports.html.twig', [
  'title' => 'Programme reports',
  'description' => 'Reports about programme item favourites.',
  'breadcrumbs' => ['Home' => '/', 'Programme reports'],
]);
