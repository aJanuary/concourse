<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/prog_handlers.php');

if (!array_key_exists('item_id', $_GET)) {
  die('Missing required query parameter "item_id"');
}

prog_join_session($_GET['item_id'], 'Go to a session', 'Go to a session');
