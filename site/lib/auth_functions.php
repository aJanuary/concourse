<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');
require_once(getenv('CONFIG_LIB_DIR') . '/discord.php');
require_once(getenv('CONFIG_LIB_DIR') . '/secure_links.php');

function session_cookie_options($expires_at) {
  $is_https = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
  );

  $options = [
    'expires' => $expires_at,
    'path' => '/',
    'secure' => $is_https,
    'httponly' => true,
    'samesite' => $is_https ? 'None' : 'Lax',
  ];

  if (trim((string) COOKIE_DOMAIN) !== '') {
    $options['domain'] = COOKIE_DOMAIN;
  }

  return $options;
}

function make_session($badge_no) {
  error_log('Creating session for badge_no ' . $badge_no);
    $session_id = sha1(rand());
    $expires_at = time() + 60*60*24*30;

    $member = conreg_get_member_by_badge_no($badge_no);
    error_log("Member for badge_no $badge_no: " . print_r($member, true));
    if ($member === null) {
      error_log("No member found with badge no $badge_no");
        throw new Exception("No member found with badge no $badge_no");
    }
    db_upsert_member($badge_no, $member['email'], $member['name'], $member['roles']);

    foreach (DEFAULT_ROLES_BY_BADGE as $role_name => $badge_nos) {
        if (in_array($badge_no, $badge_nos)) {
            db_add_member_role($badge_no, $role_name);
        }
    }

    db_insert_session($session_id, $badge_no, $expires_at);
    setcookie("session", $session_id, session_cookie_options($expires_at));
}

function make_anonymous_session() {
  $expires_at = time() + 60*60*24;
  setcookie('session', 'anonymous', session_cookie_options($expires_at));
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
  $implicit = false;
  if (isset($GLOBALS['app-data']['allow_implicit_anonymous'])) {
    $implicit = $GLOBALS['app-data']['allow_implicit_anonymous'] === 'always' || (ALLOW_ANONYMOUS_ACCESS && $GLOBALS['app-data']['allow_implicit_anonymous']);
  }
  return (
    (isset($_COOKIE['session']) && $_COOKIE['session'] == 'anonymous') ||
    ($implicit && !isset($_COOKIE['session']))
  );
}

function is_valid_redirect($url) {
  if (str_starts_with($url, '/')) {
    return true;
  }
  foreach (CONCLAR_ALLOWED_ORIGINS as $origin) {
    if ($url === $origin || str_starts_with($url, $origin . '/')) {
      return true;
    }
  }
  return false;
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

function get_current_user_email() {
  if (!isset($_COOKIE['session']) || $_COOKIE['session'] == 'anonymous') {
    return null;
  }
  return db_get_email_by_session($_COOKIE['session']);
}

function logout() {
  if (isset($_COOKIE['session'])) {
    db_delete_session($_COOKIE['session']);
  }
  setcookie('session', '', session_cookie_options(time() - 3600));
  session_destroy();
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

function current_user_has_permission($permission) {
  if (!array_key_exists('permissions', $GLOBALS['app-data'])) {
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

function send_login_code($email, $redirect) {
  if (!conreg_any_member_has_email($email)) {
    send_email($email, CON_SHORT_NAME . ' login code', 'No member found', [
      [ 'type' => 'para', 'text' => 'Hello,' ],
      [ 'type' => 'para', 'text' => 'We received a request to log in, but no member is registered with this email address.' ],
      [ 'type' => 'para', 'text' => 'If you would like to, you can [buy a membership](https://eastercon2026.org/register).' ],
      [ 'type' => 'para', 'text' => 'If you believe this is an error, and you should have a membership, please email [membership@eastercon2026.org](mailto:membership@eastercon2026.org).' ],
      [ 'type' => 'para', 'text' => "Best regards,\n\nThe Convention Team" ]
    ]);
    return 'ok';
  }

  $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
  db_insert_login_code($email, $code, $redirect, time() + 60 * 15);

  send_email($email, CON_SHORT_NAME . ' login code', 'Your member portal login code', [
    [ 'type' => 'para', 'text' => 'Your login code is:' ],
    [ 'type' => 'code', 'text' => $code ],
    [ 'type' => 'para', 'text' => "This code will expire in 15 minutes.\n\nIf you did not request this code, you can safely ignore this email." ],
  ]);
  return 'ok';
}

function send_password_email($email, $redirect, $subject, $task_start, $task_inline, $path) {
  if (!conreg_any_member_has_email($email)) {
    send_email($email, $subject, 'No member found', [
      [ 'type' => 'para', 'text' => 'Hello,' ],
      [ 'type' => 'para', 'text' => 'We received a request to ' . $task_inline . ', but no member is registered with this email address.' ],
      [ 'type' => 'para', 'text' => 'If you would like to, you can [buy a membership](https://eastercon2026.org/register).' ],
      [ 'type' => 'para', 'text' => 'If you believe this is an error, and you should have a membership, please email [membership@eastercon2026.org](mailto:membership@eastercon2026.org).' ],
      [ 'type' => 'para', 'text' => "Best regards,\n\nThe Convention Team" ]
    ]);
    return 'ok';
  }

  $link = ROOT_URL . create_secure_link(
    $path, [
    'email' => $email,
    'redirect' => $redirect,
  ]);

  send_email($email, $subject, $task_start, [
    [ 'type' => 'para', 'text' => 'Click the button below to ' . $task_inline . '.' ],
    [ 'type' => 'button', 'href' => $link, 'text' => $task_start ],
    [ 'type' => 'para', 'text' => "This link will expire in 15 minutes.\n\nIf you did not request this link, you can safely ignore this email." ],
  ]);

  return 'ok';
}

function send_set_password_email($email, $redirect) {
  return send_password_email($email, $redirect, 'Set your password for ' . CON_SHORT_NAME, 'Set your password', 'set your password', '/set-password');
}

function send_forgot_password_email($email, $redirect) {
  return send_password_email($email, $redirect, CON_SHORT_NAME . ' password reset', 'Reset your password', 'reset your password', '/forgot-password');
}
?>