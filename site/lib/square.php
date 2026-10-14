<?php

// Square, which takes the site selection fee through a payment link. The
// order's reference_id is the member's badge number, which is how the
// webhook knows whose payment it is. See docs/integrations/site-selection.md.

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php');

use Square\Checkout\PaymentLinks\Requests\CreatePaymentLinkRequest;
use Square\Checkout\PaymentLinks\Requests\GetPaymentLinksRequest;
use Square\Checkout\PaymentLinks\Requests\ListPaymentLinksRequest;
use Square\Environments;
use Square\Exceptions\SquareApiException;
use Square\Orders\Requests\GetOrdersRequest;
use Square\SquareClient;
use Square\Types\CheckoutOptions;
use Square\Types\Order;
use Square\Types\OrderLineItem;
use Square\Types\PrePopulatedData;

function square_is_production() {
  return config_get('square', 'environment', 'sandbox') === 'production';
}

function square_client() {
  static $client = null;

  if ($client === null) {
    require_config('square', 'access_token', 'location_id', 'catalog_item_variation_id');
    $client = new SquareClient(
      token: config_get_secret('square', 'access_token'),
      options: ['baseUrl' => square_is_production() ? Environments::Production->value : Environments::Sandbox->value],
    );
  }
  return $client;
}

// Where Square sends webhooks. It must be exactly the URL registered with
// Square, as it is part of the signature.
function square_notification_url() {
  return config_get('square', 'notification_url') ?: ROOT_URL . '/site-selection/square-webhook';
}

function square_payment_link_result($payment_link) {
  return ['id' => $payment_link->getId(), 'url' => $payment_link->getUrl()];
}

// The member's payment link for the site selection fee, ['id' => ..., 'url'
// => ...], creating it if they don't have one. Throws on Square errors.
function square_get_or_create_payment_link($badge_no, $email) {
  $client = square_client();

  $existing_id = db_get_latest_site_selection_payment_link($badge_no);
  if ($existing_id !== null) {
    try {
      return square_payment_link_result($client->checkout->paymentLinks->get(new GetPaymentLinksRequest(['id' => $existing_id]))->getPaymentLink());
    } catch (SquareApiException $e) {
      // Deleted in Square, perhaps; make another
    }
  }

  $note = "Site selection fee for badge $badge_no";
  // One key per member, so two requests at once can't make two links. Outside
  // production a key is per second, so a sandbox can be tried again.
  $idempotency_key = 'site-selection_' . $badge_no;
  if (!square_is_production()) {
    $idempotency_key .= '_' . time();
  }

  try {
    $response = $client->checkout->paymentLinks->create(new CreatePaymentLinkRequest([
      'idempotencyKey' => $idempotency_key,
      'description' => $note,
      'paymentNote' => $note,
      'order' => new Order([
        'locationId' => config_get('square', 'location_id'),
        'referenceId' => (string) $badge_no,
        'lineItems' => [new OrderLineItem([
          'quantity' => '1',
          'catalogObjectId' => config_get('square', 'catalog_item_variation_id'),
          'note' => $note,
        ])],
      ]),
      'prePopulatedData' => new PrePopulatedData(['buyerEmail' => $email]),
      'checkoutOptions' => new CheckoutOptions(['redirectUrl' => ROOT_URL . '/site-selection/processing']),
    ]));
  } catch (SquareApiException $e) {
    if (!square_is_idempotency_conflict($e)) {
      throw $e;
    }
    // The link was made by an earlier request: either one running at the same
    // time, which has recorded it by now, or one that failed before recording
    // it, which can only be found by looking through Square's links
    $raced_id = db_get_latest_site_selection_payment_link($badge_no);
    if ($raced_id !== null) {
      return square_payment_link_result($client->checkout->paymentLinks->get(new GetPaymentLinksRequest(['id' => $raced_id]))->getPaymentLink());
    }
    $recovered = square_find_payment_link_by_badge_no($badge_no);
    if ($recovered === null) {
      throw $e;
    }
    db_record_site_selection_payment_link($recovered->getId(), $badge_no, $recovered->getOrderId());
    return square_payment_link_result($recovered);
  }

  $payment_link = $response->getPaymentLink();
  db_record_site_selection_payment_link($payment_link->getId(), $badge_no, $payment_link->getOrderId());
  return square_payment_link_result($payment_link);
}

function square_is_idempotency_conflict(SquareApiException $e) {
  foreach ($e->getErrors() as $error) {
    if ($error->getCode() === 'IDEMPOTENCY_KEY_REUSED' || str_contains((string) $error->getDetail(), 'idempotency key has already been used')) {
      return true;
    }
  }
  return false;
}

// Square can't look a link up by idempotency key, so this pages through
// every link for the one whose order has the badge number. Slow, but only
// needed when a link was made and not recorded.
function square_find_payment_link_by_badge_no($badge_no) {
  $client = square_client();
  foreach ($client->checkout->paymentLinks->list(new ListPaymentLinksRequest(['limit' => 50])) as $payment_link) {
    if ($payment_link->getOrderId() === null) {
      continue;
    }
    $order = square_get_order($payment_link->getOrderId());
    if ($order !== null && $order->getReferenceId() === (string) $badge_no) {
      return $payment_link;
    }
  }
  return null;
}

// Null if Square doesn't have the order
function square_get_order($order_id) {
  try {
    return square_client()->orders->get(new GetOrdersRequest(['orderId' => $order_id]))->getOrder();
  } catch (SquareApiException $e) {
    if ($e->getStatusCode() === 404) {
      return null;
    }
    throw $e;
  }
}

// Square signs the notification URL followed by the body with the webhook's
// signature key
function square_verify_webhook_signature($body, $signature) {
  require_config('square', 'webhook_signature_key');
  $expected = base64_encode(hash_hmac('sha256', square_notification_url() . $body, config_get_secret('square', 'webhook_signature_key'), true));
  return hash_equals($expected, (string) $signature);
}
