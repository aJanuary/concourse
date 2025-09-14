<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;


function create_secure_link($path, $data) {
  $token = JWT::encode([
    'iss' => 'Member portal',
    'iat' => time(),
    'exp' => time() + 60*15,
    'aud' => '',
    'data' => array_merge(['path' => $path], $data)
  ], SIGNING_KEY, 'HS256');
  return $path . '?' . http_build_query(['token' => $token]);
}

function verify_secure_link($path, $token) {
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

?>