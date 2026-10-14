<?php
define('ANALYTICS_FIELD_MAX_LENGTH', 2000);

// One event per line, so newlines in a field would forge extra log entries
function sanitize_analytics_field($value) {
  $value = preg_replace('/[\x00-\x1F\x7F]/', ' ', $value);
  return mb_substr($value, 0, ANALYTICS_FIELD_MAX_LENGTH);
}

function record_analytics($type, $event, $data) {
  $log_filename = getenv('CONFIG_ANALYTICS_DIR') . '/analytics-' . date('Y-m-d-H') . '.log';
  $type = sanitize_analytics_field($type);
  $event = sanitize_analytics_field($event);
  $data = sanitize_analytics_field($data);
  file_put_contents($log_filename, date('Y-m-d\TH:i:s') . ' ' . $type . ' ' . $event . ' ' . $data . "\n", FILE_APPEND);
}
?>
