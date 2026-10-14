<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');
require_once(getenv('CONFIG_LIB_DIR') . '/square.php');

// Square calls this, without a session, when a payment changes. A completed
// payment for a site selection order assigns the member a voter key. Errors
// return 500 so that Square sends the event again later; events this doesn't
// act on return 200. Doesn't log voter keys.

function respond($status, $body) {
  http_response_code($status);
  header('Content-Type: application/json');
  echo json_encode($body);
  exit;
}

function handle_payment_updated($event) {
  $payment = $event['data']['object']['payment'] ?? null;
  if (!is_array($payment)) {
    throw new RuntimeException('payment.updated event has no payment');
  }
  $payment_id = (string) ($payment['id'] ?? '');
  $status = (string) ($payment['status'] ?? '');
  if ($status !== 'COMPLETED') {
    return;
  }

  $order_id = $payment['order_id'] ?? null;
  if (!$order_id) {
    error_log("Square webhook: payment $payment_id has no order, so isn't for site selection");
    return;
  }
  $order = square_get_order($order_id);
  if ($order === null) {
    throw new RuntimeException("Square webhook: order $order_id for payment $payment_id not found");
  }
  $badge_no = $order->getReferenceId();
  if (!$badge_no || !db_member_exists($badge_no)) {
    error_log("Square webhook: order $order_id for payment $payment_id isn't for a member, so isn't for site selection");
    return;
  }

  $result = site_selection_complete_purchase($badge_no, $order_id);
  if ($result['success']) {
    error_log("Square webhook: assigned a voter key to badge $badge_no for order $order_id");
  } else if ($result['error'] === 'already_has_token') {
    error_log("Square webhook: badge $badge_no can already vote, so order $order_id changed nothing");
  } else {
    // Square tries again for a while, so adding keys to the pool fixes it
    throw new RuntimeException("Square webhook: no voter keys left to assign to badge $badge_no for order $order_id");
  }
}

$body = file_get_contents('php://input');
try {
  if (!square_verify_webhook_signature($body, $_SERVER['HTTP_X_SQUARE_HMACSHA256_SIGNATURE'] ?? '')) {
    error_log('Square webhook: the signature is wrong. Check [square] notification_url is the URL registered with Square');
    respond(403, ['error' => 'Invalid signature']);
  }

  $event = json_decode($body, true);
  if (!is_array($event)) {
    respond(400, ['error' => 'Invalid JSON']);
  }
  if (($event['type'] ?? '') === 'payment.updated') {
    handle_payment_updated($event);
  }
  respond(200, ['status' => 'ok']);
} catch (Throwable $e) {
  $err_code = log_exception($e);
  respond(500, ['error' => "Internal error $err_code"]);
}
