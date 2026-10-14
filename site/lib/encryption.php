<?php

// AES-256-GCM encryption of values in the secrets file. The key is 32 bytes,
// base64 encoded. An encrypted value is base64 of the 12 byte nonce, the
// ciphertext and the 16 byte tag. See "Encrypted secrets" in
// docs/configuration.md.

const CONFIG_ENCRYPTION_NONCE_LENGTH = 12;
const CONFIG_ENCRYPTION_TAG_LENGTH = 16;

function config_encryption_key_bytes($key) {
  $key_bytes = base64_decode(trim($key), true);
  if ($key_bytes === false || strlen($key_bytes) !== 32) {
    throw new RuntimeException('Invalid encryption key: it must be 32 bytes, base64 encoded');
  }
  return $key_bytes;
}

function generate_config_encryption_key() {
  return base64_encode(random_bytes(32));
}

function encrypt_config_value($plaintext, $key) {
  $key_bytes = config_encryption_key_bytes($key);
  $nonce = random_bytes(CONFIG_ENCRYPTION_NONCE_LENGTH);
  $tag = '';
  $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key_bytes, OPENSSL_RAW_DATA, $nonce, $tag, '', CONFIG_ENCRYPTION_TAG_LENGTH);
  if ($ciphertext === false) {
    throw new RuntimeException('Encryption failed');
  }
  return base64_encode($nonce . $ciphertext . $tag);
}

function decrypt_config_value($encrypted, $key) {
  $key_bytes = config_encryption_key_bytes($key);
  $data = base64_decode($encrypted, true);
  if ($data === false) {
    throw new RuntimeException('Invalid encrypted value: it is not base64');
  }
  if (strlen($data) < CONFIG_ENCRYPTION_NONCE_LENGTH + CONFIG_ENCRYPTION_TAG_LENGTH) {
    throw new RuntimeException('Invalid encrypted value: it is too short');
  }

  $nonce = substr($data, 0, CONFIG_ENCRYPTION_NONCE_LENGTH);
  $tag = substr($data, -CONFIG_ENCRYPTION_TAG_LENGTH);
  $ciphertext = substr($data, CONFIG_ENCRYPTION_NONCE_LENGTH, -CONFIG_ENCRYPTION_TAG_LENGTH);
  $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key_bytes, OPENSSL_RAW_DATA, $nonce, $tag);
  if ($plaintext === false) {
    throw new RuntimeException('Decryption failed: the key is wrong or the value has been changed');
  }
  return $plaintext;
}
