<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');
require_once(getenv('CONFIG_LIB_DIR') . '/square.php');
require_once(getenv('CONFIG_LIB_DIR') . '/countries.php');

check_permission('see-site-selection');

$badge_no = get_current_user_badge_no();

if (db_get_member_site_selection($badge_no) !== null) {
  header('Location: /site-selection');
  exit;
}

$error = null;
$info = db_get_site_selection_info($badge_no);
$country = $info['country'] ?? '';
$address = $info['address'] ?? '';
$wants_paper_publications = $info['wants_paper_publications'] ?? false;
$consent_to_publish_name = $info['consent_to_publish_name'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $country = (string) ($_POST['country'] ?? '');
  $address = trim((string) ($_POST['address'] ?? ''));
  $wants_paper_publications = isset($_POST['wants_paper_publications']);
  $consent_to_publish_name = isset($_POST['consent_to_publish_name']);

  if ($address === '') {
    $error = 'Enter your postal address.';
  } else if (!array_key_exists($country, COUNTRIES)) {
    $error = 'Choose your country.';
  } else {
    db_set_site_selection_info($badge_no, $country, $address, $wants_paper_publications, $consent_to_publish_name);
    try {
      $payment_link = square_get_or_create_payment_link($badge_no, get_current_user_email());
      header('Location: ' . $payment_link['url']);
      exit;
    } catch (Exception $e) {
      $err_code = log_exception($e);
      $error = "We couldn't start your payment. Please try again, and if it still doesn't work, contact " . SUPPORT_EMAIL . " quoting error $err_code.";
    }
  }
}

$countries = COUNTRIES;
asort($countries, SORT_STRING | SORT_FLAG_CASE);

render_page('pages/site-selection/details.html.twig', [
  'error' => $error,
  'address' => $address,
  'country' => $country,
  'countries' => $countries,
  'wants_paper_publications' => $wants_paper_publications,
  'consent_to_publish_name' => $consent_to_publish_name,
]);
