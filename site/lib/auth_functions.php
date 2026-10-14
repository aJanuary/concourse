<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');
require_once(getenv('CONFIG_LIB_DIR') . '/discord.php');

// Browsers only accept SameSite=None on Secure cookies, so plain HTTP (local
// development) falls back to Lax. None over HTTPS lets a ConClar site on
// another origin send the cookie.
function session_cookie_options($expires_at) {
  $options = [
    'expires' => $expires_at,
    'path' => '/',
    'secure' => IS_HTTPS,
    'httponly' => true,
    'samesite' => IS_HTTPS ? 'None' : 'Lax',
  ];

  if (SESSION_COOKIE_DOMAIN !== '') {
    $options['domain'] = SESSION_COOKIE_DOMAIN;
  }

  return $options;
}

function make_session($badge_no) {
  $session_id = bin2hex(random_bytes(32));
  $expires_at = time() + 60*60*24*30;
  db_insert_session($session_id, $badge_no, $expires_at);
  setcookie(SESSION_COOKIE_NAME, $session_id, session_cookie_options($expires_at));
}

function make_anonymous_session() {
  $expires_at = time() + 60*60*24;
  setcookie(SESSION_COOKIE_NAME, 'anonymous', session_cookie_options($expires_at));
}

// The id of the logged in session, or null for no session or an anonymous one
function get_current_session_id() {
  if (!isset($_COOKIE[SESSION_COOKIE_NAME]) || $_COOKIE[SESSION_COOKIE_NAME] == 'anonymous') {
    return null;
  }
  return $_COOKIE[SESSION_COOKIE_NAME];
}

function is_logged_in() {
  $session_id = get_current_session_id();
  if ($session_id === null) {
    return false;
  }
  return db_session_exists($session_id);
}

function is_anonymous() {
  $implicit = false;
  if (isset($GLOBALS['app-data']['allow_implicit_anonymous'])) {
    $implicit = $GLOBALS['app-data']['allow_implicit_anonymous'] === 'always' || (ALLOW_ANONYMOUS_ACCESS && $GLOBALS['app-data']['allow_implicit_anonymous']);
  }
  return (
    (isset($_COOKIE[SESSION_COOKIE_NAME]) && $_COOKIE[SESSION_COOKIE_NAME] == 'anonymous') ||
    ($implicit && !isset($_COOKIE[SESSION_COOKIE_NAME]))
  );
}

// Only paths on this site, plus origins from allowed_redirect_origins()
function is_valid_redirect($url) {
  if (!is_string($url) || preg_match('/[\x00-\x1f\\\\]/', $url)) {
    return false;
  }
  // "//host" is a protocol-relative URL, not a path
  if (str_starts_with($url, '/')) {
    return !str_starts_with($url, '//');
  }
  foreach (allowed_redirect_origins() as $origin) {
    if ($url === $origin || str_starts_with($url, $origin . '/')) {
      return true;
    }
  }
  return false;
}

function allowed_redirect_origins() {
  return [];
}

function make_login_link() {
  return '/login?redirect=' . urlencode($_SERVER['REQUEST_URI']);
}

function get_current_user_badge_no() {
  $session_id = get_current_session_id();
  if ($session_id === null) {
    return null;
  }
  return db_get_badge_no_by_session($session_id);
}

function get_current_user_name() {
  if (!isset($_COOKIE[SESSION_COOKIE_NAME])) {
    return null;
  }
  if ($_COOKIE[SESSION_COOKIE_NAME] == 'anonymous') {
    return 'Anonymous';
  }
  return db_get_user_name_by_session($_COOKIE[SESSION_COOKIE_NAME]);
}

function get_current_user_email() {
  $session_id = get_current_session_id();
  if ($session_id === null) {
    return null;
  }
  return db_get_email_by_session($session_id);
}

function logout() {
  $session_id = get_current_session_id();
  if ($session_id !== null) {
    db_delete_session($session_id);
  }
  setcookie(SESSION_COOKIE_NAME, '', session_cookie_options(time() - 3600));
  if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
  }
}

function get_registration_status($email) {
  $member_exists = db_member_exists($email);
  return $member_exists ? "registered" : "unregistered";
}

function create_member($badge_no, $email, $name) {
  db_create_member($badge_no, $email, $name);
}

function update_member($badge_no, $email, $name) {
  $existing = db_get_member($badge_no);
  if ($existing['email'] == $email && $existing['name'] == $name) {
    return;
  }

  db_update_member($badge_no, $email, $name);

  if ($existing['name'] != $name) {
    $discord_ids = db_get_discord_ids($badge_no);
    foreach ($discord_ids as $discord_id) {
      request_discord_recheck_user($discord_id);
    }
  }
}

function get_login_link_status($badge_no, $login_code) {
  $expires = db_get_login_link_expiry($badge_no, $login_code);
  if ($expires == null) {
    return "no-found";
  }
  if ($expires < time()) {
    return "expired";
  }
  return "ok";
}

function current_user_has_permission($permission) {
  if (!array_key_exists('permissions', $GLOBALS['app-data'] ?? [])) {
    return false;
  }
  return in_array($permission, $GLOBALS['app-data']['permissions']);
}

function member_type() {
  if (is_anonymous()) {
    return 'anonymous';
  }
  if (!is_logged_in()) {
    return 'not-logged-in';
  }
  if (current_user_has_permission('online')) {
    return 'online';
  }
  if (current_user_has_permission('in-person')) {
    return 'in-person';
  }
  return 'unknown';
}
?>