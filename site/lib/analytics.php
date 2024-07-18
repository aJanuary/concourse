<?php
function record_analytics($type, $event, $data) {
  $log_filename = getenv('CONFIG_ANALYTICS_DIR') . '/analytics-' . date('Y-m-d-H') . '.log';
  file_put_contents($log_filename, date('Y-m-d\TH:i:s') . ' ' . $type . ' ' . $event . ' ' . $data . "\n", FILE_APPEND);
}
?>