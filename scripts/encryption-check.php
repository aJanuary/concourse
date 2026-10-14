<?php
//
// Checks encrypting and decrypting secrets-file values, and each place
// config_get_secret() can find the key. Run in the web container:
//
//   docker compose exec -T -e HTTP_HOST=localhost web php < scripts/encryption-check.php
//
// Exits non-zero on failure.
//

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/encryption.php');

restore_exception_handler();

$failures = 0;

function check($description, $condition) {
  global $failures;
  if ($condition) {
    echo "ok   $description\n";
  } else {
    echo "FAIL $description\n";
    $failures++;
  }
}

// The message of the exception $fn throws, or null if it doesn't
function error_from($fn) {
  try {
    $fn();
  } catch (RuntimeException $e) {
    return $e->getMessage();
  }
  return null;
}

// Each check sets the key sources it needs
function clear_key_sources() {
  global $config_file;
  putenv('CONFIG_ENCRYPTION_KEY');
  putenv('CONFIG_ENCRYPTION_KEY_FILE');
  unset($config_file['encryption']);
}

$key = generate_config_encryption_key();
$other_key = generate_config_encryption_key();
$secret = "s3cret value with \"quotes\" and ; = signs";

$encrypted = encrypt_config_value($secret, $key);
check('decrypt_config_value round trip', decrypt_config_value($encrypted, $key) === $secret);
check('encrypt_config_value uses a fresh nonce', encrypt_config_value($secret, $key) !== $encrypted);
check('round trip of an empty value', decrypt_config_value(encrypt_config_value('', $key), $key) === '');
check('decrypting with the wrong key fails', error_from(fn() => decrypt_config_value($encrypted, $other_key)) !== null);
$data = base64_decode($encrypted);
$data[15] = chr(ord($data[15]) ^ 1);
check('decrypting a tampered value fails', error_from(fn() => decrypt_config_value(base64_encode($data), $key)) !== null);
check('decrypting a short value fails', error_from(fn() => decrypt_config_value(base64_encode('short'), $key)) !== null);
check('a key of the wrong length is refused', error_from(fn() => encrypt_config_value($secret, base64_encode('too short'))) !== null);

// Upstream's format, without the enc: prefix
check('decrypts a value encrypted by LAcon V\'s script',
  decrypt_config_value('AAECAwQFBgcICQoLL2e6d6onl/81O4oEbSgjbc2dBXu7', 'AAECAwQFBgcICQoLDA0ODxAREhMUFRYXGBkaGxwdHh8=') === 'hello');

$original_config = $config_file;
$config_file['smoke'] = [
  'plain' => 'plain value',
  'encrypted' => CONFIG_ENCRYPTED_PREFIX . $encrypted,
  'other_key' => CONFIG_ENCRYPTED_PREFIX . encrypt_config_value($secret, $other_key),
];

clear_key_sources();
check('a plain value is returned as it is without a key', config_get_secret('smoke', 'plain') === 'plain value');
check('a missing value gives the default', config_get_secret('smoke', 'missing', 'default') === 'default');
$error = error_from(fn() => config_get_secret('smoke', 'encrypted'));
check('an encrypted value without a key names the setting', $error !== null && str_contains($error, "'encrypted' in the [smoke] section"));

clear_key_sources();
putenv("CONFIG_ENCRYPTION_KEY=$key");
check('key from CONFIG_ENCRYPTION_KEY', config_get_secret('smoke', 'encrypted') === $secret);
check('a plain value is returned as it is with a key', config_get_secret('smoke', 'plain') === 'plain value');
$error = error_from(fn() => config_get_secret('smoke', 'other_key'));
check('a value encrypted with another key names the setting', $error !== null && str_contains($error, "Can't decrypt 'other_key' in the [smoke] section"));

clear_key_sources();
$key_file = tempnam(sys_get_temp_dir(), 'key');
file_put_contents($key_file, "$key\n");
putenv("CONFIG_ENCRYPTION_KEY_FILE=$key_file");
check('key from CONFIG_ENCRYPTION_KEY_FILE', config_encryption_key() === $key);
unlink($key_file);
check('a missing key file is an error', error_from(fn() => config_encryption_key()) !== null);

clear_key_sources();
$config_file['encryption'] = ['config_encryption_key' => $key];
check('key from [encryption] config_encryption_key', config_encryption_key() === $key);

clear_key_sources();
putenv("CONFIG_ENCRYPTION_KEY=$key");
$config_file['encryption'] = ['config_encryption_key' => $other_key];
check('CONFIG_ENCRYPTION_KEY comes before the secrets file', config_encryption_key() === $key);

clear_key_sources();
$config_file = $original_config;

if ($failures > 0) {
  echo "\n$failures check(s) failed\n";
  exit(1);
}
echo "\nAll checks passed\n";
