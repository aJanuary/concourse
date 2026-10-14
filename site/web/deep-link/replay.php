<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/prog_handlers.php');

if (!array_key_exists('item_id', $_GET)) {
  die('Missing required query parameter "item_id"');
}

prog_open_replay($_GET['item_id'], 'Go to an item replay', 'Go to an item replay');
