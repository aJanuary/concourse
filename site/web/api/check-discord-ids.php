<?php

require_once('../../includes/config.php');
require_once('../../includes/db.php');

class ClientException extends Exception {
  public $public_error_code;

  public function __construct($public_error_code, $message, $code = 0, Throwable $previous = null) {
      $this->public_error_code = $public_error_code;
      parent::__construct($message, $code, $previous);
  }
}

class AuthorizationException extends Exception { }

function validate_signature() {
  $headers = array_change_key_case(apache_request_headers(), CASE_LOWER);
  if (!isset($headers["authorization"])) {
    throw new AuthorizationException("Missing required Authorization header");
  }
  $auth_header = $headers["authorization"];
  $auth_header_parts = explode(" ", $auth_header, 3);

  if ($auth_header_parts[0] != "members:1") {
    throw new AuthorizationException("Invalid authorization header");
  }

  if (!isset($headers["x-members-requesttime"])) {
    throw new AuthorizationException("Missing required X-Members-RequestTime header");
  }

  $client_name = $auth_header_parts[1];
  if (!isset(API_KEYS[$client_name])) {
    throw new AuthorizationException($unauthorized_error_msg);
  }

  $request_time_str = $headers["x-members-requesttime"];
  $request_time = DateTimeImmutable::createFromFormat("Y-m-d\TH:i:s\Z", $request_time_str);
  if (!$request_time) {
    throw new AuthorizationException("Malformed X-Members-RequestTime header");
  }
  $tolerance = new DateInterval("PT5M");
  $lower_bound = $request_time->sub($tolerance);
  $upper_bound = $request_time->add($tolerance);
  $now = new DateTimeImmutable("NOW");
  // We don't want clients weakening the security by trying to make requests with a timestamp far in the future.
  if ($now < $lower_bound || $now > $upper_bound) {
    throw new AuthorizationException("Request authorization has timed out");
  }

  $webhook_data = strtoupper($_SERVER["REQUEST_METHOD"]) . "\n" . $_SERVER["REQUEST_URI"] . "\n" . $request_time_str . "\n" . base64_encode(file_get_contents("php://input"));
  foreach (API_KEYS[$client_name] as $secret) {
    $webhook_sig = hash_hmac("sha256", $webhook_data, $secret);
    if ($webhook_sig == $auth_header_parts[2]) {
        return;
    }
  }

  throw new AuthorizationException("Invalid signature");
}

function send_error($http_status, $code, $error, $instance=null) {
  http_response_code($http_status);
  header("Content-Type: application/json; charset=utf-8");
  $resp = array(
      "code" => $code,
      "error" => $error
  );
  if (!is_null($instance)) {
      $resp["instance"] = $instance;
  }
  echo json_encode($resp);
}

try {
  validate_signature();

  if ($_SERVER["CONTENT_TYPE"] != "application/json") {
    throw new ClientException("ERR_MALFORMED_BODY", "Content-Type must be 'application/json'");
  }
  $json = file_get_contents('php://input');
  $data = json_decode($json, true);
  if (!$data) {
      throw new ClientException("ERR_MALFORMED_BODY", "Error parsing content as json");
  }

  $discord_ids = db_get_discord_ids();
  $missing_ids = array_diff($data["discordUserIds"], $discord_ids);
  error_log("DAta: " . print_r($data, true));

  http_response_code(200);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode([
    "missing" => $missing_ids
  ]);
} catch (ClientException $e) {
  send_error(400, $e->public_error_code, $e->getMessage());
} catch (AuthorizationException $e) {
  // Log auth errors in case we need to spot people brute forcing
  $instance = log_exception($e);
  send_error(401, "ERR_AUTH", $e->getMessage(), $instance);
} catch (Exception $e) {
  $instance = log_exception($e);
  send_error(500, "ERR_INTERNAL", "An internal error occurred", $instance);
}
?>