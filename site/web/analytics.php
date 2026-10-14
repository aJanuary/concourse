<?php
$_REQUEST['no_redirect'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/analytics.php');

$event = $_GET['event'] ?? '';
$data = $_GET['data'] ?? '';
if (!is_string($event) || !is_string($data)) {
  exit;
}
record_analytics(member_type(), $event, $data);
?>
