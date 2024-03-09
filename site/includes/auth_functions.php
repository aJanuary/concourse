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

function get_registration_status($email) {
  $member_exists = db_member_exists($email);
  if ($member_exists) {
    return "registered";
  }

  // Login
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://registration.eastercon2024.co.uk/user/login');
  curl_setopt($ch, CURLOPT_POST, 1);
  $headers = [
      'Content-Type: application/x-www-form-urlencoded',
  ];
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $data = 'name=' . CONREG_USERNAME . '&pass=' . CONREG_PASSWORD . '&form_id=user_login_form&op=Log+in&antibot_key=OuGabhEnsQCUCR95JficG3FK_Za41XClnU4XV3i7-M4';
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  $response = curl_exec($ch);

  if (curl_errno($ch)) {
    throw new Exception(curl_error($ch));
  } else {
      preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
      $cookies = array();
      foreach($matches[1] as $item) {
          parse_str($item, $cookie);
          $cookies = array_merge($cookies, $cookie);
      }
  }
  curl_close($ch);

  // Get memberlist
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://registration.eastercon2024.co.uk/admin/members/memberlist');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  $headers = [
      'Cookie: ' . http_build_query($cookies, '', '; '),
  ];
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $response = curl_exec($ch);

  if (curl_errno($ch)) {
    throw new Exception(curl_error($ch));
  }

  curl_close($ch);

  // Check if they are in the memberlist
  $dom = new DOMDocument();
  @$dom->loadHTML($response);
  $xpath = new DOMXPath($dom);
  $rows = $xpath->query('(//table)[2]/tbody/tr');
  foreach ($rows as $row) {
    $cols = $row->getElementsByTagName('td');
    $member_email = $cols->item(5)->textContent;
    if ($member_email == $email) {
      if ($cols->item(19)->textContent != 'Yes') {
        return "pending";
      }
      $type = $cols->item(1)->textContent;
      $badge_no = $cols->item(2)->textContent;
      $name = $cols->item(6)->textContent;
      create_member($badge_no, $email, $name, $type);
      return "registered";
    }
  }

  return "unregistered";
}

function create_member($badge_no, $email, $name, $type) {
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

  db_create_member($badge_no, $name, $email, $type, $magic_link);
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