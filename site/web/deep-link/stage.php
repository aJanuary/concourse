<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/prog_handlers.php');

if (!array_key_exists('room_id', $_GET)) {
  die('Missing required query parameter "room_id"');
}

$title = 'Go to a stage';
prog_check_test_mode($title);
if (!is_logged_in()) {
  prog_render_login($title);
}
prog_check_may_join($title);

$stage = db_get_stage($_GET['room_id']);
if (!$stage['viewer_url']) {
  prog_render_message($title, 'Unknown room', "Sorry, we don't know about that room.");
}

header('Location: ' . $stage['viewer_url']);
exit;
