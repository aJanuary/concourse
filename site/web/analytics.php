<?php
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

$event = $_GET['event'];
$data = $_GET['data'];
db_record_analytics(member_type(), $event, $data);
?>