<?php

require_once('secrets.php');
require_once('db.php');
require_once('requests.php');

function is_logged_in() {
  if (!isset($_COOKIE['session'])) {
    return false;
  }
  return db_session_exists($_COOKIE['session']);
}

function get_current_user_badge_no() {
  if (!isset($_COOKIE['session'])) {
    return null;
  }
  return db_get_badge_no_by_session($_COOKIE['session']);
}

function get_current_user_name() {
  if (!isset($_COOKIE['session'])) {
    return null;
  }
  return db_get_user_name_by_session($_COOKIE['session']);
}

function login($email, $password) {
  if (!db_check_member_creds($email, $password)) {
    return false;
  }

  $session_id = sha1(rand());
  $expires_at = time() + 60*60*24*30;
  db_insert_session($session_id, $email, $expires_at);
  setcookie("session", $session_id, $expires_at, '/', '', true, true);

  return true;
}

function logout() {
  if (isset($_COOKIE['session'])) {
    db_delete_session($_COOKIE['session']);
  }
  setcookie('session', '', time() - 3600, '/');
}

function member_is_registered($email) {
  $member_exists = db_member_exists($email);
  if ($member_exists) {
    return true;
  }

  // Don't have it hooked up to ConReg yet. For now, invent members for anyone with an @eastercon2024.co.uk email address.
  // Just for testing.

  if (!preg_match('/@eastercon2024.co.uk$/', $email)) {
    return false;
  }

  $badge_no = sprintf('%04d', rand(0, 9999));
  $name = explode('@', $email)[0];
  
  create_member($badge_no, $email, $name);
  return true;
}

function create_member($badge_no, $email, $name) {
  $magic_link_resp = api_call('https://api.events.ringcentral.com/v1/tickets/' . HOPIN_TICKET_ID . '/magicLinks', [
    'Authorization: Bearer ' . HOPIN_API_KEY,
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
  if (!isset($_COOKIE['session'])) {
    return false;
  }
  return db_has_permission($_COOKIE['session'], $permission);
}

?>