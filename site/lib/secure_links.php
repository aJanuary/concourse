<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

// Signed tokens are bound to the path they were issued for, so a token from
// one page cannot be replayed on another.
function create_secure_token($path, $data, $ttl = 900) {
  require_config('crypto', 'signing_key');

  return JWT::encode([
    'iss' => 'Member portal',
    'iat' => time(),
    'exp' => time() + $ttl,
    'aud' => '',
    'data' => array_merge(['path' => $path], $data)
  ], SIGNING_KEY, 'HS256');
}

function create_secure_link($path, $data, $ttl = 900) {
  return $path . '?' . http_build_query(['token' => create_secure_token($path, $data, $ttl)]);
}

// The token's data as an array, or 'expired' or 'invalid'
function verify_secure_link($path, $token) {
  require_config('crypto', 'signing_key');

  try {
    $decoded = JWT::decode($token, new Key(SIGNING_KEY, 'HS256'));
    if ($decoded->data->path !== $path) {
      return 'invalid';
    }
    return (array) $decoded->data;
  } catch (ExpiredException $e) {
    return 'expired';
  } catch (Exception $e) {
    return 'invalid';
  }
}
