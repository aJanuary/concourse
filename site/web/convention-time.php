<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

check_permission('see-time');

render_page('pages/convention-time.html.twig', [
  'title' => 'Convention time',
  'description' => 'Convert times to and from ' . CON_NAME . ' time.',
  'breadcrumbs' => ['Home' => '/', 'Convention time'],
  'timezone' => TIMEZONE,
  'utc_offset' => (new DateTime('now', new DateTimeZone(TIMEZONE)))->format('P'),
]);
