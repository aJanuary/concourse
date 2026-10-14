<?php

function csv_safe_cell($value) {
  if ($value === null) {
    return '';
  }

  $string = (string) $value;

  // Prevent spreadsheet formula execution when opening CSV exports
  if (preg_match('/^[\s\x00-\x1F]*[=+\-@]/', $string)) {
    return "'" . $string;
  }

  return $string;
}
