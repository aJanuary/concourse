<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/prog_handlers.php');

if (!array_key_exists('room_id', $_GET)) {
  die('Missing required query parameter "room_id"');
}

prog_join_room_stream($_GET['room_id'], 'View stream', 'View stream');
