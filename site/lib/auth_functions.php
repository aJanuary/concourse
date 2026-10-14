<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');
require_once(getenv('CONFIG_LIB_DIR') . '/discord.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/Identity.php');

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

function make_session($badge_no, $sid = null, $sub = null, $id_token = null) {
  $session_id = bin2hex(random_bytes(32));
  $expires_at = time() + 60*60*24*30;
  db_insert_session($session_id, $badge_no, $expires_at);
  if ($sub !== null) {
    db_insert_oidc_session($session_id, $sid, $sub, $id_token);
  }
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

function create_member($badge_no, $email, $name, $real_name = null) {
  db_create_member($badge_no, $email, $name, $real_name);
}

// A null $real_name leaves the stored one alone
function update_member($badge_no, $email, $name, $real_name = null) {
  $existing = db_get_member($badge_no);
  if ($existing['email'] == $email && $existing['name'] == $name && ($real_name === null || $existing['real_name'] == $real_name)) {
    return;
  }

  db_update_member($badge_no, $email, $name, $real_name);

  if ($existing['name'] != $name) {
    $discord_ids = db_get_discord_ids($badge_no);
    foreach ($discord_ids as $discord_id) {
      request_discord_recheck_user($discord_id);
    }
  }
}

// Completes a login for an identity from a provider: creates or updates the
// member, starts a session and sends the browser on. Throws AuthFailure if the
// identity may not log in.
function finish_login($identity, $redirect) {
  if (ALLOWLIST && !in_array($identity->email, ALLOWLIST)) {
    throw new AuthFailure('not-in-allowlist');
  }

  $badge_no = $identity->badge_no;
  if (!db_member_exists($badge_no)) {
    create_member($badge_no, $identity->email, $identity->name, $identity->real_name);
  } else {
    update_member($badge_no, $identity->email, $identity->name, $identity->real_name);
  }

  $roles = $identity->roles ?? [];
  foreach (DEFAULT_ROLES_BY_BADGE as $role => $badge_nos) {
    if (in_array($badge_no, $badge_nos, true)) {
      $roles[] = $role;
    }
  }
  $existing_roles = db_get_member_role_names($badge_no);
  $roles = array_unique($roles);
  foreach (array_diff($roles, $existing_roles) as $role) {
    db_add_member_role($badge_no, $role);
  }
  foreach (array_diff(array_intersect($identity->managed_roles ?? [], $existing_roles), $roles) as $role) {
    db_remove_member_role($badge_no, $role);
  }

  make_session($badge_no, $identity->sid, $identity->sub, $identity->id_token);

  if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
  }

  // Don't allow open redirects
  // https://cheatsheetseries.owasp.org/cheatsheets/Unvalidated_Redirects_and_Forwards_Cheat_Sheet.html
  $redirect = is_valid_redirect($redirect) ? $redirect : '/';
  if ($identity->offer_password) {
    $redirect = '/password-prompt?redirect=' . urlencode($redirect);
  }
  header('Location: ' . $redirect);
  exit;
}

function redirect_to_login_error($error_code, $redirect = null) {
  $url = '/login?error_code=' . urlencode($error_code);
  if ($redirect !== null) {
    $url .= '&redirect=' . urlencode($redirect);
  }
  header('Location: ' . $url);
  exit;
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