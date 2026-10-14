<?php

function api_call($url, $headers, $payload=false, $method=null, $timeout=null) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  if ($payload) {
    if ($method) {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    } else {
      curl_setopt($ch, CURLOPT_POST, true);
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  if ($timeout !== null) {
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  }
  $response_raw = curl_exec($ch);
  return json_decode($response_raw, true);
}

?>