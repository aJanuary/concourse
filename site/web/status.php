<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

try {
  db_get_num_members();
} catch (Exception $e) {
  http_response_code(500);
  exit;
}
?>