<?php

require_once('db.php');
require_once('requests.php');

function make_session($badge_no) {
    $session_id = sha1(rand());
    $expires_at = time() + 60*60*24*30;
    db_insert_session($session_id, $badge_no, $expires_at);
    setcookie("session", $session_id, $expires_at, '/', '', true, true);
}

function make_anonymous_session() {
  $expires_at = time() + 60*60*24;
  setcookie('session', 'anonymous', $expires_at, '/', '', true, true);
}

function is_logged_in() {
  if (!isset($_COOKIE['session'])) {
    return false;
  }
  if ($_COOKIE['session'] == 'anonymous') {
    return false;
  }
  return db_session_exists($_COOKIE['session']);
}

function is_anonymous() {
  $implicit = isset($_REQUEST['allow_implicit_anonymous']) && $_REQUEST['allow_implicit_anonymous'];
  return (
    (isset($_COOKIE['session']) && $_COOKIE['session'] == 'anonymous') ||
    ($implicit && !isset($_COOKIE['session']))
  );
}

function make_login_link() {
  return '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']);
}

function get_current_user_badge_no() {
  if (!isset($_COOKIE['session']) || $_COOKIE['session'] == 'anonymous') {
    return null;
  }
  return db_get_badge_no_by_session($_COOKIE['session']);
}

function get_current_user_name() {
  if (!isset($_COOKIE['session'])) {
    return null;
  }
  if ($_COOKIE['session'] == 'anonymous') {
    return 'Anonymous';
  }
  return db_get_user_name_by_session($_COOKIE['session']);
}

function logout() {
  if (isset($_COOKIE['session'])) {
    db_delete_session($_COOKIE['session']);
  }
  setcookie('session', '', time() - 3600, '/');
}

function get_registration_status($email) {
  $member_exists = db_member_exists($email);
  if ($member_exists) {
    return "registered";
  }

  // Get name and domain from email
  $email_parts = explode('@', $email);
  $name = $email_parts[0];
  $domain = $email_parts[1];

  if ($domain != "glasgow2024.org") {
    return "unregistered";
  }

  // Until we have OAuth integration, create a member for anyone with an @glasgow email
  $badge_no = bin2hex(random_bytes(2));
  create_member($badge_no, $email, $name);

  return "registered";
}

function create_member($badge_no, $email, $name) {
  $magic_link_resp = api_call('https://api.events.ringcentral.com/v1/tickets/' . RCE_TICKET_ID . '/magicLinks', [
    'Authorization: Bearer ' . RCE_API_KEY,
    'Content-Type: application/json'
  ], json_encode([
    'data' => [
      'type' => 'magicLink',
      'attributes' => [
        'email' => $email,
        'firstName' => $name,
        'headline' => $badge_no,
        'lastName' => '-',
        'registrationId' => $badge_no
      ]
    ]
  ]));
  $magic_link = $magic_link_resp['data']['attributes']['magicLink'];

  db_create_member($badge_no, $name, $email, $magic_link);
}

function get_login_link_status($email, $login_code) {
  $expires = db_get_login_link_expiry($email, $login_code);
  if ($expires == null) {
    return "no-found";
  }
  if ($expires < time()) {
    return "expired";
  }
  return "ok";
}

function current_user_has_permission($permission) {
  return in_array($permission, $_REQUEST['permissions']);
}

?>