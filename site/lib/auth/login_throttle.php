<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

const LOGIN_THROTTLE_WINDOW = 15 * 60;

// Failures (or requests, for mail) allowed in the window, per email and per
// client address. The address limits are higher as one address can be shared.
const LOGIN_THROTTLE_LIMITS = [
  'code-request' => ['email' => 5, 'ip' => 20],
  'code-verify' => ['email' => 10, 'ip' => 30],
  'password' => ['email' => 10, 'ip' => 30],
  'password-email' => ['email' => 5, 'ip' => 20],
];

function login_client_ip() {
  if (CLIENT_IP_HEADER !== '') {
    $forwarded = trim(explode(',', $_SERVER[CLIENT_IP_HEADER] ?? '')[0]);
    if (filter_var($forwarded, FILTER_VALIDATE_IP) !== false) {
      return $forwarded;
    }
  }
  return $_SERVER['REMOTE_ADDR'] ?? '';
}

function login_throttle_subjects($email) {
  return [
    'email' => hash('sha256', 'email:' . strtolower($email)),
    'ip' => hash('sha256', 'ip:' . login_client_ip()),
  ];
}

function login_throttle_blocked($bucket, $email) {
  foreach (login_throttle_subjects($email) as $kind => $subject) {
    if (db_count_login_attempts($bucket, $subject, LOGIN_THROTTLE_WINDOW) >= LOGIN_THROTTLE_LIMITS[$bucket][$kind]) {
      return true;
    }
  }
  return false;
}

function login_throttle_hit($bucket, $email) {
  foreach (login_throttle_subjects($email) as $subject) {
    db_record_login_attempt($bucket, $subject);
  }
}
