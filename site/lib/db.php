<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db_dialect.php');

function db_dsn() {
  $port = DB_PORT ? ';port=' . DB_PORT : '';
  if (DB_DRIVER === 'pgsql') {
    return 'pgsql:host=' . DB_HOST . $port . ';dbname=' . DB_NAME;
  }
  return 'mysql:host=' . DB_HOST . $port . ';dbname=' . DB_NAME . ';charset=utf8mb4';
}

$pdo = new PDO(
  db_dsn(),
  DB_USER,
  DB_PASSWORD,
  [
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Real prepared statements, so MySQL returns native ints like Postgres does
    PDO::ATTR_EMULATE_PREPARES => false,
  ]
);

function db_query($sql, $params = []) {
  global $pdo;

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  return $stmt;
}

// First column of the first row, or null if there are no rows
function db_fetch_value($sql, $params = []) {
  $value = db_query($sql, $params)->fetchColumn();
  return $value === false ? null : $value;
}

// First row, or null if there are no rows
function db_fetch_row($sql, $params = []) {
  $row = db_query($sql, $params)->fetch();
  return $row === false ? null : $row;
}

function db_fetch_rows($sql, $params = []) {
  return db_query($sql, $params)->fetchAll();
}

function db_fetch_column($sql, $params = []) {
  return db_query($sql, $params)->fetchAll(PDO::FETCH_COLUMN);
}

function db_check_member_creds($email, $password) {
  $hash = db_fetch_value("SELECT password FROM members WHERE LOWER(email) = LOWER(?)", [$email]);
  if ($hash === null) {
    // No such user
    return false;
  }

  return password_verify($password, $hash);
}

function db_get_member($badge_no) {
  $row = db_fetch_row("SELECT name, email, real_name FROM members WHERE badge_no = ?", [$badge_no]);

  return [
    'name' => $row['name'] ?? null,
    'email' => $row['email'] ?? null,
    'real_name' => $row['real_name'] ?? null,
  ];
}

function db_get_members() {
  return db_fetch_rows("SELECT badge_no, name, email FROM members ORDER BY badge_no");
}

function db_insert_session($session_token, $badge_no, $expires_at) {
  db_query(
    "INSERT INTO sessions (session_id, expires_at, badge_no) VALUES (?, ?, ?)",
    [$session_token, date('Y-m-d H:i:s', $expires_at), $badge_no]
  );
}

function db_session_exists($session_token) {
  return db_fetch_value("SELECT 1 FROM sessions WHERE session_id = ? AND expires_at > NOW()", [$session_token]) !== null;
}

function db_get_badge_no_by_session($session_token) {
  return db_fetch_value("SELECT badge_no FROM sessions WHERE session_id = ? AND expires_at > NOW()", [$session_token]);
}

function db_get_email_by_session($session_token) {
  return db_fetch_value("SELECT email FROM members WHERE badge_no = (SELECT badge_no FROM sessions WHERE session_id = ? AND expires_at > NOW())", [$session_token]);
}

function db_get_user_name_by_session($session_token) {
  return db_fetch_value("SELECT name FROM members WHERE badge_no = (SELECT badge_no FROM sessions WHERE session_id = ? AND expires_at > NOW())", [$session_token]);
}

function db_get_sessions() {
  return db_fetch_rows("SELECT badge_no, name, session_id FROM sessions JOIN members USING (badge_no) ORDER BY badge_no, session_id");
}

function db_delete_session($session_token) {
  db_query("DELETE FROM sessions WHERE session_id = ?", [$session_token]);
}

function db_insert_oidc_session($session_token, $sid, $sub, $id_token) {
  db_query("INSERT INTO oidc_sessions (session_id, sid, sub, id_token) VALUES (?, ?, ?, ?)", [$session_token, $sid, $sub, $id_token]);
}

function db_oidc_session_exists($session_token) {
  return db_fetch_value("SELECT 1 FROM oidc_sessions WHERE session_id = ?", [$session_token]) !== null;
}

function db_get_oidc_id_token($session_token) {
  return db_fetch_value("SELECT id_token FROM oidc_sessions WHERE session_id = ?", [$session_token]);
}

// Both return how many sessions were deleted. The oidc_sessions rows go with them.
function db_delete_sessions_by_sid($sid) {
  return db_query("DELETE FROM sessions WHERE session_id IN (SELECT session_id FROM oidc_sessions WHERE sid = ?)", [$sid])->rowCount();
}

function db_delete_sessions_by_sub($sub) {
  return db_query("DELETE FROM sessions WHERE session_id IN (SELECT session_id FROM oidc_sessions WHERE sub = ?)", [$sub])->rowCount();
}

function db_create_member($badge_no, $email, $name, $real_name = null) {
  db_query("INSERT INTO members (badge_no, email, name, real_name) VALUES (?, ?, ?, ?)", [$badge_no, $email, $name, $real_name]);
}

function db_update_member($badge_no, $email, $name, $real_name = null) {
  db_query("UPDATE members SET name = ?, email = ?, real_name = COALESCE(?, real_name) WHERE badge_no = ?", [$name, $email, $real_name, $badge_no]);
}

function db_set_discord_id($badge_no, $discord_id, $discord_username) {
  db_upsert(
    'discord_ids',
    ['badge_no', 'discord_id'],
    ['username'],
    ['badge_no' => $badge_no, 'discord_id' => $discord_id, 'username' => $discord_username]
  );
}

function db_member_exists($badge_no) {
  return db_fetch_value("SELECT 1 FROM members WHERE badge_no = ?", [$badge_no]);
}

function db_get_members_by_email($email) {
  return db_fetch_rows("SELECT badge_no, name, email FROM members WHERE LOWER(email) = LOWER(?) ORDER BY badge_no", [$email]);
}

// Passwords and login codes are keyed by the lower-cased email so that both
// databases treat differently-cased spellings as one account.
function db_check_password_auth($email, $password) {
  $email = strtolower($email);
  $hash = db_fetch_value("SELECT password_hash FROM password_auth WHERE email = ?", [$email]);
  if ($hash === null) {
    return false;
  }

  if (!password_verify($password, $hash)) {
    return false;
  }

  if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
    db_set_password_auth($email, $password);
  }

  return true;
}

function db_email_has_password_auth($email) {
  return db_fetch_value("SELECT 1 FROM password_auth WHERE email = ?", [strtolower($email)]) !== null;
}

function db_set_password_auth($email, $password) {
  db_upsert(
    'password_auth',
    ['email'],
    ['password_hash', 'last_edited'],
    [
      'email' => strtolower($email),
      'password_hash' => password_hash($password, PASSWORD_DEFAULT),
      'last_edited' => date('Y-m-d H:i:s'),
    ]
  );
}

function db_password_prompt_dismissed($email) {
  return db_fetch_value("SELECT 1 FROM password_prompt_dismissals WHERE email = ?", [strtolower($email)]) !== null;
}

function db_dismiss_password_prompt($email) {
  db_upsert('password_prompt_dismissals', ['email'], [], ['email' => strtolower($email)]);
}

// Only the most recent code for an email can be used
function db_create_login_code($email, $code_hash, $expires_at) {
  $email = strtolower($email);
  db_query("DELETE FROM login_codes WHERE email = ? OR expires_at < ?", [$email, date('Y-m-d H:i:s')]);
  db_query(
    "INSERT INTO login_codes (email, code_hash, expires_at) VALUES (?, ?, ?)",
    [$email, $code_hash, date('Y-m-d H:i:s', $expires_at)]
  );
}

// Checks a code and consumes it when correct. A code that has been guessed
// wrongly $max_failures times is deleted, so it cannot be brute forced.
function db_consume_login_code($email, $code_hash, $max_failures) {
  $email = strtolower($email);
  $row = db_fetch_row(
    "SELECT id, code_hash, failed_attempts FROM login_codes WHERE email = ? AND expires_at > ? ORDER BY id DESC",
    [$email, date('Y-m-d H:i:s')]
  );
  if ($row === null) {
    return false;
  }

  if (hash_equals($row['code_hash'], $code_hash)) {
    db_query("DELETE FROM login_codes WHERE email = ?", [$email]);
    return true;
  }

  if ($row['failed_attempts'] + 1 >= $max_failures) {
    db_query("DELETE FROM login_codes WHERE id = ?", [$row['id']]);
  } else {
    db_query("UPDATE login_codes SET failed_attempts = failed_attempts + 1 WHERE id = ?", [$row['id']]);
  }
  return false;
}

function db_record_login_attempt($bucket, $subject) {
  $now = time();
  db_query("DELETE FROM login_attempts WHERE created_at < ?", [date('Y-m-d H:i:s', $now - 60*60*24)]);
  db_query(
    "INSERT INTO login_attempts (bucket, subject, created_at) VALUES (?, ?, ?)",
    [$bucket, $subject, date('Y-m-d H:i:s', $now)]
  );
}

function db_count_login_attempts($bucket, $subject, $window_seconds) {
  return (int) db_fetch_value(
    "SELECT COUNT(*) FROM login_attempts WHERE bucket = ? AND subject = ? AND created_at > ?",
    [$bucket, $subject, date('Y-m-d H:i:s', time() - $window_seconds)]
  );
}

function db_get_member_discord_data() {
  $rows = db_fetch_rows("SELECT members.badge_no, name, discord_ids.discord_id, discord_ids.username FROM members LEFT OUTER JOIN discord_ids USING (badge_no) ORDER BY badge_no");

  $members = [];
  $cur_member = null;
  foreach ($rows as $row) {
    if ($cur_member && $cur_member['badge_no'] === $row['badge_no']) {
      $cur_member['discord_ids'][] = ['id' => $row['discord_id'], 'username' => $row['username']];
    } else {
      if ($cur_member) {
        $members[] = $cur_member;
      }
      $cur_member = ['badge_no' => $row['badge_no'], 'name' => $row['name'], 'discord_ids' => []];
      if ($row['discord_id']) {
        $cur_member['discord_ids'][] = ['id' => $row['discord_id'], 'username' => $row['username']];
      }
    }
  }
  if ($cur_member) {
    $members[] = $cur_member;
  }

  return $members;
}

function db_get_all_discord_info() {
  $rows = db_fetch_rows("SELECT members.name AS member_name, discord_id, roles.name AS role_name FROM discord_ids JOIN members USING (badge_no) LEFT JOIN member_roles USING (badge_no) LEFT JOIN roles USING (role_id)");

  $discord_info = [];
  foreach ($rows as $row) {
    if (!isset($discord_info[$row['discord_id']])) {
      $discord_info[$row['discord_id']] = [
        "name" => $row['member_name'],
        "roles" => [],
      ];
    }
    if ($row['role_name']) {
      $discord_info[$row['discord_id']]["roles"][] = $row['role_name'];
    }
  }

  return $discord_info;
}

function db_get_discord_usernames($session_id) {
  return db_fetch_column("SELECT discord_ids.username FROM discord_ids JOIN sessions USING (badge_no) WHERE session_id = ?", [$session_id]);
}

function db_get_discord_ids($badge_no) {
  return db_fetch_column("SELECT discord_id FROM discord_ids WHERE badge_no = ?", [$badge_no]);
}

function db_get_roles() {
  return db_fetch_rows("SELECT role_id, name FROM roles");
}

// The admin role has every permission, including ones added after it was set
// up, whatever role_permissions says
const ADMIN_ROLE_NAME = 'admin';
const GRANTED_ROLE_PERMISSIONS = "(SELECT role_id, permission_id FROM role_permissions UNION SELECT role_id, permission_id FROM roles CROSS JOIN permissions WHERE roles.name = '" . ADMIN_ROLE_NAME . "') granted";

function db_get_role_permissions($role_id) {
  $rows = db_fetch_rows("SELECT permission_id, name, COALESCE(has_permission, 0) AS has_permission FROM permissions LEFT OUTER JOIN (SELECT permission_id, COUNT(*) AS has_permission FROM " . GRANTED_ROLE_PERMISSIONS . " WHERE role_id = ? GROUP BY permission_id) h_p USING (permission_id) ORDER BY name", [$role_id]);

  return array_map(function ($row) {
    return [
      'permission_id' => $row['permission_id'],
      'name' => $row['name'],
      'has_permission' => (int) $row['has_permission'],
    ];
  }, $rows);
}

function db_set_role_permission($role_id, $permission_id, $has_permission) {
  if ($has_permission) {
    db_upsert(
      'role_permissions',
      ['role_id', 'permission_id'],
      [],
      ['role_id' => $role_id, 'permission_id' => $permission_id]
    );
  } else {
    db_query("DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?", [$role_id, $permission_id]);
  }
}

function db_create_role($name) {
  db_query("INSERT INTO roles (name) VALUES (?)", [$name]);

  return db_last_insert_id('roles_role_id_seq');
}

function db_delete_role($role_id) {
  db_query("DELETE FROM roles WHERE role_id = ?", [$role_id]);
}

function db_add_member_role($badge_no, $role_name) {
  db_query("INSERT INTO member_roles (badge_no, role_id) SELECT ?, role_id FROM roles WHERE name = ?", [$badge_no, $role_name]);
}

function db_remove_member_role($badge_no, $role_name) {
  db_query("DELETE FROM member_roles WHERE badge_no = ? AND role_id IN (SELECT role_id FROM roles WHERE name = ?)", [$badge_no, $role_name]);
}

function db_get_member_role_names($badge_no) {
  return db_fetch_column("SELECT roles.name FROM roles JOIN member_roles USING (role_id) WHERE badge_no = ?", [$badge_no]);
}

// Badge numbers match exactly; names and emails match anywhere
function db_search_members($query, $limit = 50) {
  $like = '%' . strtr($query, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
  return db_fetch_rows(
    "SELECT badge_no, name, email FROM members WHERE badge_no = ? OR LOWER(name) LIKE LOWER(?) ESCAPE '!' OR LOWER(real_name) LIKE LOWER(?) ESCAPE '!' OR LOWER(email) LIKE LOWER(?) ESCAPE '!' ORDER BY badge_no LIMIT " . (int) $limit,
    [$query, $like, $like, $like]
  );
}

function db_get_role($role_id) {
  return db_fetch_row("SELECT role_id, name FROM roles WHERE role_id = ?", [$role_id]);
}

// By id, because role names are not unique
function db_get_member_roles($badge_no) {
  return db_fetch_rows("SELECT role_id, roles.name FROM roles JOIN member_roles USING (role_id) WHERE badge_no = ? ORDER BY roles.name", [$badge_no]);
}

// Returns false if the member already had the role
function db_grant_member_role($badge_no, $role_id) {
  return db_insert_ignore('member_roles', ['badge_no' => $badge_no, 'role_id' => $role_id]);
}

// Returns false if the member did not have the role
function db_revoke_member_role($badge_no, $role_id) {
  return db_query("DELETE FROM member_roles WHERE badge_no = ? AND role_id = ?", [$badge_no, $role_id])->rowCount() > 0;
}

// The member's permissions as they would be without one of their roles
function db_get_member_permissions_without_role($badge_no, $role_id) {
  return db_fetch_column("(SELECT name FROM permissions JOIN " . GRANTED_ROLE_PERMISSIONS . " USING (permission_id) JOIN member_roles USING (role_id) WHERE badge_no = ? AND role_id <> ?) UNION (SELECT permissions.name FROM permissions JOIN " . GRANTED_ROLE_PERMISSIONS . " USING (permission_id) JOIN roles USING (role_id) WHERE roles.name = 'default')", [$badge_no, $role_id]);
}

function db_get_default_permissions() {
  return db_fetch_column("SELECT permissions.name FROM permissions JOIN " . GRANTED_ROLE_PERMISSIONS . " USING (permission_id) JOIN roles USING (role_id) WHERE roles.name = 'default'");
}

function db_get_permissions_by_session($session_id) {
  return db_fetch_column("(SELECT name FROM permissions JOIN " . GRANTED_ROLE_PERMISSIONS . " USING (permission_id) JOIN member_roles USING (role_id) JOIN sessions USING (badge_no) WHERE session_id = ?) UNION (SELECT permissions.name FROM permissions JOIN " . GRANTED_ROLE_PERMISSIONS . " USING (permission_id) JOIN roles USING (role_id) WHERE roles.name = 'default')", [$session_id]);
}

function db_get_role_id($role_name) {
  return db_fetch_value("SELECT role_id FROM roles WHERE name = ?", [$role_name]);
}

function db_get_stages() {
  return db_fetch_rows("SELECT room_id, title FROM prog_stages ORDER BY title, room_id");
}

function db_get_stage($room_id) {
  $row = db_fetch_row("SELECT title, viewer_url, type, participant_url FROM prog_stages WHERE room_id = ?", [$room_id]);

  return [
    'title' => $row['title'] ?? null,
    'viewer_url' => $row['viewer_url'] ?? null,
    'type' => $row['type'] ?? null,
    'participant_url' => $row['participant_url'] ?? null,
  ];
}

function db_add_stage($room_id, $title, $viewer_url, $type, $participant_url) {
  db_query(
    "INSERT INTO prog_stages (room_id, title, viewer_url, type, participant_url) VALUES (?, ?, ?, ?, ?)",
    [$room_id, $title, $viewer_url, $type, $participant_url]
  );
}

function db_edit_stage($room_id, $title, $viewer_url, $type, $participant_url) {
  db_query(
    "UPDATE prog_stages SET title = ?, viewer_url = ?, type = ?, participant_url = ? WHERE room_id = ?",
    [$title, $viewer_url, $type, $participant_url, $room_id]
  );
}

function db_delete_stage($room_id) {
  db_query("DELETE FROM prog_stages WHERE room_id = ?", [$room_id]);
}

function db_get_stream_urls() {
  return db_fetch_rows("SELECT room_id, title, url FROM prog_stream_urls ORDER BY COALESCE(NULLIF(title, ''), room_id)");
}

function db_get_stream_url($room_id) {
  $row = db_fetch_row("SELECT title, url FROM prog_stream_urls WHERE room_id = ?", [$room_id]);

  return [
    'found' => $row !== null,
    'title' => $row['title'] ?? null,
    'url' => $row['url'] ?? null,
  ];
}

function db_add_stream_url($room_id, $title, $url) {
  db_query("INSERT INTO prog_stream_urls (room_id, title, url) VALUES (?, ?, ?)", [$room_id, $title, $url]);
}

function db_edit_stream_url($room_id, $title, $url) {
  db_query("UPDATE prog_stream_urls SET title = ?, url = ? WHERE room_id = ?", [$title, $url, $room_id]);
}

function db_delete_stream_url($room_id) {
  db_query("DELETE FROM prog_stream_urls WHERE room_id = ?", [$room_id]);
}

function db_get_prog_sessions() {
  return db_fetch_rows("SELECT item_id, title FROM prog_sessions ORDER BY title, item_id");
}

function db_get_prog_session($item_id) {
  $row = db_fetch_row("SELECT title, destination, handler FROM prog_sessions WHERE item_id = ?", [$item_id]);

  return [
    'found' => $row !== null && $row !== false,
    'title' => $row['title'] ?? null,
    'destination' => $row['destination'] ?? null,
    'handler' => $row['handler'] ?? null,
  ];
}

function db_edit_prog_session($item_id, $title, $destination, $handler) {
  db_query("UPDATE prog_sessions SET title = ?, destination = ?, handler = ? WHERE item_id = ?", [$title, $destination, $handler, $item_id]);
}

function db_add_prog_session($item_id, $title, $destination, $handler) {
  db_query("INSERT INTO prog_sessions (item_id, title, destination, handler) VALUES (?, ?, ?, ?)", [$item_id, $title, $destination, $handler]);
}

function db_upsert_prog_session($item_id, $title, $destination, $handler) {
  db_upsert(
    'prog_sessions',
    ['item_id'],
    ['title', 'destination', 'handler'],
    ['item_id' => $item_id, 'title' => $title, 'destination' => $destination, 'handler' => $handler]
  );
}

function db_delete_prog_session($item_id) {
  db_query("DELETE FROM prog_sessions WHERE item_id = ?", [$item_id]);
}

function db_get_discord_posts() {
  return db_fetch_rows("SELECT item_id, title FROM prog_discord_posts ORDER BY title, item_id");
}

function db_format_discord_post($row) {
  $start_time = $row['start'] ?? null;
  $duration_secs = $row['duration'] ?? null;

  return [
    'start' => $start_time ? (new DateTime('@' . $start_time))->setTimezone(new DateTimeZone(TIMEZONE))->format('Y-m-d H:i') : null,
    'title' => $row['title'] ?? null,
    'duration' => $duration_secs ? $duration_secs / 60 : null,
    'room_id' => $row['room_id'] ?? null,
    'post_url' => $row['post_url'] ?? null,
  ];
}

function db_get_discord_post($item_id) {
  return db_format_discord_post(
    db_fetch_row("SELECT start, title, duration, room_id, post_url FROM prog_discord_posts WHERE item_id = ?", [$item_id])
  );
}

function db_get_discord_post_by_room_and_time($room_id, $utc_time) {
  return db_format_discord_post(
    db_fetch_row("SELECT title, start, duration, room_id, post_url FROM prog_discord_posts WHERE room_id = ? AND start <= ? AND start + duration >= ?", [$room_id, $utc_time, $utc_time])
  );
}

function db_edit_discord_post($item_id, $title, $start, $duration, $room_id, $post_url) {
  if ($start) {
    $date = new DateTime($start, new DateTimeZone(TIMEZONE));
    $start_time = $date->getTimestamp();
  } else {
    $start_time = null;
  }

  if ($duration) {
    $duration_secs = intval($duration) * 60;
  } else {
    $duration_secs = null;
  }

  db_query(
    "UPDATE prog_discord_posts SET title = ?, start = ?, duration = ?, room_id = ?, post_url = ? WHERE item_id = ?",
    [$title, $start_time, $duration_secs, $room_id, $post_url, $item_id]
  );
}

function db_add_discord_post($item_id, $title, $start, $duration, $room_id, $post_url) {
  if ($start) {
    $date = new DateTime($start, new DateTimeZone(TIMEZONE));
    $start_time = $date->getTimestamp();
  } else {
    $start_time = null;
  }

  if ($duration) {
    $duration_secs = intval($duration) * 60;
  } else {
    $duration_secs = null;
  }

  db_query(
    "INSERT INTO prog_discord_posts (item_id, title, start, duration, room_id, post_url) VALUES (?, ?, ?, ?, ?, ?)",
    [$item_id, $title, $start_time, $duration_secs, $room_id, $post_url]
  );
}

function db_upsert_discord_post($item_id, $title, $start, $duration, $room_id, $post_url) {
  if ($start) {
    $date = new DateTime($start, new DateTimeZone("UTC"));
    $start_time = $date->getTimestamp();
  } else {
    $start_time = null;
  }

  if ($duration) {
    $duration_secs = intval($duration) * 60;
  } else {
    $duration_secs = null;
  }

  db_upsert(
    'prog_discord_posts',
    ['item_id'],
    ['start', 'duration', 'room_id', 'post_url'],
    [
      'item_id' => $item_id,
      'title' => $title,
      'start' => $start_time,
      'duration' => $duration_secs,
      'room_id' => $room_id,
      'post_url' => $post_url,
    ]
  );
}

function db_delete_discord_post($item_id) {
  db_query("DELETE FROM prog_discord_posts WHERE item_id = ?", [$item_id]);
}

function db_get_zoom_url() {
  return db_fetch_value("SELECT zoom_url FROM prog_zoom");
}

function db_set_zoom_url($url) {
  db_query("UPDATE prog_zoom SET zoom_url = ?", [$url]);
}

function db_get_replays() {
  return db_fetch_rows("SELECT item_id, title FROM prog_replay ORDER BY title, item_id");
}

function db_get_replay($item_id) {
  $row = db_fetch_row("SELECT title, replay_url, unavailable FROM prog_replay WHERE item_id = ?", [$item_id]);

  return [
    'found' => $row !== null && $row !== false,
    'title' => $row['title'] ?? null,
    'replay_url' => $row['replay_url'] ?? null,
    'unavailable' => (bool) ($row['unavailable'] ?? false),
  ];
}

function db_edit_replay($item_id, $title, $replay_url, $unavailable) {
  db_query("UPDATE prog_replay SET title = ?, replay_url = ?, unavailable = ? WHERE item_id = ?", [$title, $replay_url, ($unavailable ? 1 : 0), $item_id]);
}

function db_add_replay($item_id, $title, $replay_url, $unavailable) {
  db_query("INSERT INTO prog_replay (item_id, title, replay_url, unavailable) VALUES (?, ?, ?, ?)", [$item_id, $title, $replay_url, ($unavailable ? 1 : 0)]);
}

function db_delete_replay($item_id) {
  db_query("DELETE FROM prog_replay WHERE item_id = ?", [$item_id]);
}

function db_get_signups() {
  return db_fetch_rows("SELECT id, title, category, url, guide_url, notes, session_start, opens_at, closes_at FROM signups");
}

// Null if there is no such signup
function db_get_signup($id) {
  return db_fetch_row("SELECT id, title, category, url, guide_url, notes, session_start, opens_at, closes_at FROM signups WHERE id = ?", [$id]);
}

function db_signup_values($signup) {
  return [
    $signup['title'],
    $signup['category'],
    $signup['url'],
    $signup['guide_url'] ?? null,
    $signup['notes'] ?? null,
    $signup['session_start'] ?? null,
    $signup['opens_at'] ?? null,
    $signup['closes_at'] ?? null,
  ];
}

function db_add_signup($signup) {
  db_query(
    "INSERT INTO signups (title, category, url, guide_url, notes, session_start, opens_at, closes_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
    db_signup_values($signup)
  );

  return db_last_insert_id('signups_id_seq');
}

// All or nothing, so a failure part way through a bulk add leaves no partial import
function db_add_signups($signups) {
  global $pdo;

  $pdo->beginTransaction();
  try {
    foreach ($signups as $signup) {
      db_add_signup($signup);
    }
    $pdo->commit();
  } catch (Exception $exception) {
    $pdo->rollBack();
    throw $exception;
  }
}

function db_edit_signup($id, $signup) {
  db_query(
    "UPDATE signups SET title = ?, category = ?, url = ?, guide_url = ?, notes = ?, session_start = ?, opens_at = ?, closes_at = ? WHERE id = ?",
    array_merge(db_signup_values($signup), [$id])
  );
}

function db_delete_signup($id) {
  db_query("DELETE FROM signups WHERE id = ?", [$id]);
}

function db_insert_discord_token($token, $discord_id, $discord_username) {
  db_query(
    "INSERT INTO discord_verification_token (token_id, discord_id, username) VALUES (?, ?, ?)",
    [$token, $discord_id, $discord_username]
  );
}

function db_set_discord_id_from_token($badge_no, $token) {
  global $pdo;

  $pdo->beginTransaction();
  try {
    $row = db_fetch_row("SELECT discord_id, username, verified_at FROM discord_verification_token WHERE token_id = ?", [$token]);
    if (!$row) {
      $pdo->rollBack();
      return [ "result"=>"no-token" ];
    }

    if ($row['verified_at']) {
      $pdo->rollBack();
      return [ "result"=>"already-verified", "id"=>$row['discord_id'], "username"=>$row['username'] ];
    }

    db_set_discord_id($badge_no, $row['discord_id'], $row['username']);

    db_query("UPDATE discord_verification_token SET verified_at = CURRENT_TIMESTAMP WHERE token_id = ?", [$token]);

    $pdo->commit();

    return [ "result"=>"verified", "id"=>$row['discord_id'], "username"=>$row['username'] ];
  } catch (PDOException $exception) {
    $pdo->rollBack();
    throw $exception;
  }
}

function db_get_discord_id_from_token($token) {
  $username = db_fetch_value("SELECT username FROM discord_verification_token WHERE token_id = ?", [$token]);

  error_log("Got username $username for token $token");
  return $username;
}

function db_record_analytics($member_type, $event, $data) {
  db_query("INSERT INTO analytics (member_type, event, data) VALUES (?, ?, ?)", [$member_type, $event, $data]);
}

function db_get_num_members() {
  return db_fetch_value("SELECT COUNT(*) FROM members");
}

function db_get_zoom_events_ticket($badge_no) {
  return db_fetch_row("SELECT ticket_id, event_join_link, email FROM zoom_events_tickets WHERE badge_no = ?", [$badge_no]);
}

// Keeps the existing ticket if there is one
function db_save_zoom_events_ticket($badge_no, $ticket_id, $event_join_link, $email) {
  db_upsert(
    'zoom_events_tickets',
    ['badge_no'],
    [],
    ['badge_no' => $badge_no, 'ticket_id' => $ticket_id, 'event_join_link' => $event_join_link, 'email' => $email]
  );
}

function db_get_cached_auth_token($service) {
  $row = db_fetch_row("SELECT auth_token, expires_at FROM auth_token_cache WHERE service = ?", [$service]);
  if ($row === null) {
    return null;
  }
  return ['auth_token' => $row['auth_token'], 'expires_at' => (int) $row['expires_at']];
}

function db_cache_auth_token($service, $auth_token, $expires_at) {
  db_upsert(
    'auth_token_cache',
    ['service'],
    ['auth_token', 'expires_at'],
    ['service' => $service, 'auth_token' => $auth_token, 'expires_at' => $expires_at]
  );
}
?>
