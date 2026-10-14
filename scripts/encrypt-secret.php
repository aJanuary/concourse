<?php
//
// Generates encryption keys and encrypts values for the secrets file. Run by
// scripts/encrypt-secret, which explains the arguments, or in the image as
// /var/scripts/encrypt-secret.php.
//

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/encryption.php');

// Errors go to the terminal, not the site's HTML error page
restore_exception_handler();

function fail($message) {
  // STDERR is not defined when the script comes in on stdin
  file_put_contents('php://stderr', "$message\n");
  exit(1);
}

$args = array_slice($_SERVER['argv'], 1);
if ($args === ['--generate-key']) {
  echo generate_config_encryption_key() . "\n";
  exit(0);
}
if ($args !== []) {
  fail('usage: scripts/encrypt-secret [--generate-key]');
}

$key = config_encryption_key();
if ($key === '') {
  fail('No encryption key is set. Set CONFIG_ENCRYPTION_KEY, CONFIG_ENCRYPTION_KEY_FILE or [encryption] config_encryption_key; see "Encrypted secrets" in docs/configuration.md');
}
// scripts/encrypt-secret passes the value in CONFIG_SECRET_VALUE, as it sends
// this script on stdin. Run directly, it reads the value from stdin.
$value = getenv('CONFIG_SECRET_VALUE');
if ($value === false) {
  if (stream_isatty(STDIN)) {
    file_put_contents('php://stderr', 'Value to encrypt: ');
    shell_exec('stty -echo');
    $value = rtrim((string) fgets(STDIN), "\r\n");
    shell_exec('stty echo');
    file_put_contents('php://stderr', "\n");
  } else {
    $value = stream_get_contents(STDIN);
  }
}
if ($value === '') {
  fail('There is no value to encrypt');
}

try {
  // Quoted, as the secrets file can't hold base64's = signs otherwise
  echo '"' . CONFIG_ENCRYPTED_PREFIX . encrypt_config_value($value, $key) . '"' . "\n";
} catch (RuntimeException $e) {
  fail($e->getMessage());
}
