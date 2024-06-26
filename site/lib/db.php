<?php

require_once('config.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

function db_check_member_creds($email, $password) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT password FROM members WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->bind_result($hash);
  if (!$stmt->fetch()) {
    // No such user
    return false;
  }
  $stmt->close();

  return password_verify($password, $hash);
}

function db_insert_session($session_token, $badge_no, $expires_at) {
  global $mysqli;

  $stmt = $mysqli->prepare("INSERT INTO sessions (session_id, expires_at, badge_no) VALUES (?, ?, ?)");
  $expires_at_ts = date('Y-m-d H:i:s', $expires_at);
  $stmt->bind_param("sss", $session_token, $expires_at_ts, $badge_no);
  $stmt->execute();
  $stmt->close();
}

function db_session_exists($session_token) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT 1 FROM sessions WHERE session_id = ? AND expires_at > NOW()");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->bind_result($email);
  if (!$stmt->fetch()) {
    // No such session
    return false;
  }
  $stmt->close();

  return true;
}

function db_get_badge_no_by_session($session_token) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT badge_no FROM sessions WHERE session_id = ? AND expires_at > NOW()");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->bind_result($badge_no);
  if (!$stmt->fetch()) {
    // No such session
    return null;
  }
  $stmt->close();

  return $badge_no;
}

function db_get_user_name_by_session($session_token) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT name FROM members WHERE badge_no = (SELECT badge_no FROM sessions WHERE session_id = ? AND expires_at > NOW())");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->bind_result($name);
  if (!$stmt->fetch()) {
    // No such session
    return null;
  }
  $stmt->close();

  return $name;
}

function db_delete_session($session_token) {
  global $mysqli;

  $stmt = $mysqli->prepare("DELETE FROM sessions WHERE session_id = ?");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->close();
}

function db_create_oauth_identity($badge_id, $provider,  $identity_id, $email, $raw_info) {
  global $mysqli;

  $mysqli->begin_transaction();
  try {
    $identity_stmt = $mysqli->prepare("INSERT INTO oauth_identities (badge_no, provider, identity_id, email, raw_info) VALUES (?, ?, ?, ?, ?)");
    $identity_stmt->bind_param("sssss", $badge_id, $provider,  $identity_id, $email, $raw_info);
    $identity_stmt->execute();
    $identity_stmt->close();

    $mysqli->commit();
  } catch (mysqli_sql_exception $exception) {
    $mysqli->rollback();
    throw $exception;
  }
}

function db_create_member($badge_id, $name, $email, $invite_url) {
  global $mysqli;

  $mysqli->begin_transaction();
  try {
    $members_stmt = $mysqli->prepare("INSERT INTO members (badge_no, name, email) VALUES (?, ?, ?)");
    $members_stmt->bind_param("sss", $badge_id, $name, $email);
    $members_stmt->execute();
    $members_stmt->close();

    $invites_stmt = $mysqli->prepare("INSERT INTO rce_invites (badge_no, invite_url) VALUES (?, ?)");
    $invites_stmt->bind_param("ss", $badge_id, $invite_url);
    $invites_stmt->execute();
    $invites_stmt->close();

    $mysqli->commit();
  } catch (mysqli_sql_exception $exception) {
    $mysqli->rollback();
    throw $exception;
  }
}

function db_get_magic_link($session_token) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT invite_url FROM rce_invites WHERE badge_no = (SELECT badge_no FROM sessions WHERE session_id = ? AND expires_at > NOW())");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->bind_result($invite_url);
  if (!$stmt->fetch()) {
    // No such session
    return null;
  }
  $stmt->close();

  return $invite_url;
}

function db_set_discord_id($badge_no, $discord_id, $discord_username) {
  global $mysqli;

  $stmt = $mysqli->prepare("REPLACE INTO discord_ids(badge_no, discord_id, username) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $badge_no, $discord_id, $discord_username);
  $stmt->execute();
  $stmt->close();
}

function db_identity_exists($badge_no) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT 1 FROM oauth_identities WHERE badge_no = ?");
  $stmt->bind_param("s", $badge_no);
  $stmt->execute();
  $stmt->bind_result($exists);
  $stmt->fetch();
  $stmt->close();

  return $exists;
}

function db_member_exists($badge_no) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT 1 FROM members WHERE badge_no = ?");
  $stmt->bind_param("s", $badge_no);
  $stmt->execute();
  $stmt->bind_result($exists);
  $stmt->fetch();
  $stmt->close();

  return $exists;
}

function db_insert_login_link($email, $login_code, $expires_at) {
  global $mysqli;

  $expires_at_ts = date('Y-m-d H:i:s', $expires_at);
  $stmt = $mysqli->prepare("INSERT INTO login_links (login_code, badge_no, expires_at) SELECT ?, badge_no, ? FROM members WHERE email = ?");
  $stmt->bind_param("sss", $login_code, $expires_at_ts, $email);
  $stmt->execute();
  $stmt->close();
}

function db_get_login_link_expiry($badge_no, $login_code) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT expires_at FROM login_links WHERE login_code = ? AND badge_no = ?");
  $stmt->bind_param("ss", $login_code, $badge_no);
  $stmt->execute();
  $stmt->bind_result($expires_at);
  if (!$stmt->fetch()) {
    // No such link
    return null;
  }
  $stmt->close();

  return strtotime($expires_at);
}

function db_get_member_discord_data() {
  global $mysqli;

  $result = $mysqli->query("SELECT members.badge_no, name, discord_ids.discord_id, discord_ids.username from members LEFT OUTER JOIN discord_ids USING (badge_no) ORDER by badge_no");
  $members = [];
  $cur_member = null;
  while ($row = $result->fetch_assoc()) {
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
  $result->close();

  return $members;
}

function db_get_all_discord_info() {
  global $mysqli;

  $result = $mysqli->query("SELECT badge_no, members.name member_name, discord_id, roles.name role_name FROM discord_ids JOIN members USING (badge_no) LEFT JOIN member_roles USING (badge_no) LEFT JOIN roles USING (role_id)");
  $discord_info = [];
  while ($row = $result->fetch_assoc()) {
    if (!isset($discord_info[$row['discord_id']])) {
      $discord_info[$row['discord_id']] = [
        "name" => $row['member_name'],
        "badge_no" => $row['badge_no'],
        "roles" => [],
      ];
    }
    if ($row['role_name']) {
      $discord_info[$row['discord_id']]["roles"][] = $row['role_name'];
    }
  }
  $result->close();

  return $discord_info;
}

function db_get_rce_links() {
  global $mysqli;

  $result = $mysqli->query("SELECT members.badge_no, name, invite_url from members LEFT OUTER JOIN rce_invites USING (badge_no) ORDER by badge_no");
  $members = [];
  while ($row = $result->fetch_assoc()) {
    $members[] = ['badge_no' => $row['badge_no'], 'name' => $row['name'], 'invite_url' => $row['invite_url']];
  }
  $result->close();

  return $members;
}

function db_get_discord_usernames($session_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT discord_ids.username FROM discord_ids JOIN sessions USING (badge_no) WHERE session_id = ?");
  $stmt->bind_param("s", $session_id);
  $stmt->execute();
  $stmt->bind_result($username);
  $usernames = [];
  while ($stmt->fetch()) {
    $usernames[] = $username;
  }
  $stmt->close();

  return $usernames;
}

function db_get_roles() {
  global $mysqli;

  $result = $mysqli->query("SELECT role_id, name FROM roles");
  $roles = [];
  while ($row = $result->fetch_assoc()) {
    $roles[] = $row;
  }
  $result->close();

  return $roles;
}

function db_get_role_permissions($role_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT permission_id, name, IFNULL(has_permission, 0) has_permission FROM permissions LEFT OUTER JOIN (SELECT permission_id, COUNT(*) has_permission FROM role_permissions WHERE role_id=? GROUP BY permission_id) h_p USING (permission_id) ORDER BY name;");
  $stmt->bind_param("s", $role_id);
  $stmt->execute();
  $stmt->bind_result($permission_id, $name, $has_permission);
  $permissions = [];
  while ($stmt->fetch()) {
    $permissions[] = ['permission_id' => $permission_id, 'name' => $name, 'has_permission' => $has_permission];
  }
  $stmt->close();

  return $permissions;
}

function db_set_role_permission($role_id, $permission_id, $has_permission) {
  global $mysqli;

  if ($has_permission) {
    $stmt = $mysqli->prepare("REPLACE INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $role_id, $permission_id);
    $stmt->execute();
    $stmt->close();
  } else {
    $stmt = $mysqli->prepare("DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?");
    $stmt->bind_param("ss", $role_id, $permission_id);
    $stmt->execute();
    $stmt->close();
  }
}

function db_create_role($name) {
  global $mysqli;

  $stmt = $mysqli->prepare("INSERT INTO roles (name) VALUES (?)");
  $stmt->bind_param("s", $name);
  $stmt->execute();
  $stmt->close();

  return $mysqli->insert_id;
}

function db_delete_role($role_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("DELETE FROM roles WHERE role_id = ?");
  $stmt->bind_param("s", $role_id);
  $stmt->execute();
  $stmt->close();
}

function db_get_default_permissions() {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT permissions.name FROM permissions JOIN role_permissions USING (permission_id) JOIN roles USING (role_id) WHERE roles.name = 'default'");
  $stmt->execute();
  $stmt->bind_result($name);
  $permissions = [];
  while ($stmt->fetch()) {
    $permissions[] = $name;
  }
  $stmt->close();

  return $permissions;
}

function db_get_permissions_by_session($session_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("(SELECT name FROM permissions JOIN role_permissions USING (permission_id) JOIN member_roles USING (role_id) JOIN sessions USING (badge_no) WHERE session_id = ?) UNION (SELECT permissions.name FROM permissions JOIN role_permissions USING (permission_id) JOIN roles USING (role_id) WHERE roles.name = 'default')");
  $stmt->bind_param("s", $session_id);
  $stmt->execute();
  $stmt->bind_result($name);
  $permissions = [];
  while ($stmt->fetch()) {
    $permissions[] = $name;
  }
  $stmt->close();

  return $permissions;
}

function db_get_role_id($role_name) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT role_id FROM roles WHERE name = ?");
  $stmt->bind_param("s", $role_name);
  $stmt->execute();
  $stmt->bind_result($role_id);
  $stmt->fetch();
  $stmt->close();

  return $role_id;
}

function db_get_stages() {
  global $mysqli;

  $result = $mysqli->query("SELECT room_id, title FROM prog_stages ORDER BY title, room_id");
  $stages = [];
  while ($row = $result->fetch_assoc()) {
    $stages[] = $row;
  }
  $result->close();

  return $stages;
}

function db_get_stage($room_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT title, viewer_url, type, participant_url FROM prog_stages WHERE room_id = ?");
  $stmt->bind_param("s", $room_id);
  $stmt->execute();
  $stmt->bind_result($title, $viewer_url, $type, $participant_url);
  $stmt->fetch();
  $stmt->close();

  return [
    'title' => $title,
    'viewer_url' => $viewer_url,
    'type' => $type,
    'participant_url' => $participant_url
  ];
}

function db_add_stage($room_id, $title, $viewer_url, $type, $participant_url) {
  global $mysqli;

  $stmt = $mysqli->prepare("INSERT INTO prog_stages (room_id, title, viewer_url, type, participant_url) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $room_id, $title, $viewer_url, $type, $participant_url);
  $stmt->execute();
  $stmt->close();

  return $mysqli->insert_id;
}

function db_edit_stage($room_id, $title, $viewer_url, $type, $participant_url) {
  global $mysqli;

  $stmt = $mysqli->prepare("UPDATE prog_stages SET title = ?, viewer_url = ?, type = ?, participant_url = ? WHERE room_id = ?");
  $stmt->bind_param("sssss", $title, $viewer_url, $type, $participant_url, $room_id);
  $stmt->execute();
  $stmt->close();
}

function db_delete_stage($room_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("DELETE FROM prog_stages WHERE room_id = ?");
  $stmt->bind_param("s", $room_id);
  $stmt->execute();
  $stmt->close();
}

function db_get_prog_sessions() {
  global $mysqli;

  $result = $mysqli->query("SELECT item_id, title FROM prog_sessions ORDER BY title, item_id");
  $items = [];
  while ($row = $result->fetch_assoc()) {
    $items[] = $row;
  }
  $result->close();

  return $items;
}

function db_get_prog_session($item_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT title, rce_url FROM prog_sessions WHERE item_id = ?");
  $stmt->bind_param("s", $item_id);
  $stmt->execute();
  $stmt->bind_result($title, $rce_url);
  $stmt->fetch();
  $stmt->close();

  return [
    'title' => $title,
    'rce_url' => $rce_url,
  ];
}

function db_edit_prog_session($item_id, $title, $rce_url) {
  global $mysqli;

  $stmt = $mysqli->prepare("UPDATE prog_sessions SET title = ?, rce_url = ? WHERE item_id = ?");
  $stmt->bind_param("sss", $title, $rce_url, $item_id);
  $stmt->execute();
  $stmt->close();
}

function db_add_prog_session($item_id, $title, $rce_url) {
  global $mysqli;

  $stmt = $mysqli->prepare("INSERT INTO prog_sessions (item_id, title, rce_url) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $item_id, $title, $rce_url);
  $stmt->execute();
  $stmt->close();

  return $mysqli->insert_id;
}

function db_delete_prog_session($item_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("DELETE FROM prog_sessions WHERE item_id = ?");
  $stmt->bind_param("s", $item_id);
  $stmt->execute();
  $stmt->close();
}

function db_get_discord_posts() {
  global $mysqli;

  $result = $mysqli->query("SELECT item_id, title FROM prog_discord_posts ORDER BY title, item_id");
  $items = [];
  while ($row = $result->fetch_assoc()) {
    $items[] = $row;
  }
  $result->close();

  return $items;
}

function db_get_discord_post($item_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT start, title, duration, room_id, post_url FROM prog_discord_posts WHERE item_id = ?");
  $stmt->bind_param("s", $item_id);
  $stmt->execute();
  $stmt->bind_result($start_time, $title, $duration_secs, $room_id, $post_url);
  $stmt->fetch();
  $stmt->close();

  $start_time_formatted = $start_time ? (new DateTime('@' . $start_time))->setTimezone(new DateTimeZone(TIMEZONE))->format('Y-m-d H:i') : null;

  return [
    'start' => $start_time_formatted,
    'title' => $title,
    'duration' => $duration_secs ? $duration_secs / 60 : null,
    'room_id' => $room_id,
    'post_url' => $post_url,
  ];
}

function db_get_discord_post_by_room_and_time($room_id, $utc_time) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT title, start, duration, room_id, post_url FROM prog_discord_posts WHERE room_id = ? AND start <= ? AND start + duration >= ?");
  $stmt->bind_param("sii", $room_id, $utc_time, $utc_time);
  $stmt->execute();
  $stmt->bind_result($title, $start_time, $duration_secs, $room_id, $post_url);
  $stmt->fetch();
  $stmt->close();

  $start_time_formatted = $start_time ? (new DateTime('@' . $start_time))->setTimezone(new DateTimeZone(TIMEZONE))->format('Y-m-d H:i') : null;

  return [
    'start' => $start_time_formatted,
    'title' => $title,
    'duration' => $duration_secs ? $duration_secs / 60 : null,
    'room_id' => $room_id,
    'post_url' => $post_url,
  ];
}

function db_edit_discord_post($item_id, $title, $start, $duration, $room_id, $post_url) {
  global $mysqli;

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

  $stmt = $mysqli->prepare("UPDATE prog_discord_posts SET title = ?, start = ?, duration = ?, room_id = ?, post_url = ? WHERE item_id = ?");
  $stmt->bind_param("siisss", $title, $start_time, $duration_secs, $room_id, $post_url, $item_id);
  $stmt->execute();
  $stmt->close();
}

function db_add_discord_post($item_id, $title, $start, $duration, $room_id, $post_url) {
  global $mysqli;

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

  $stmt = $mysqli->prepare("INSERT INTO prog_discord_posts (item_id, title, start, duration, room_id, post_url) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssiiss", $item_id, $title, $start_time, $duration_secs, $room_id, $post_url);
  $stmt->execute();
  $stmt->close();

  return $mysqli->insert_id;
}

function db_upsert_discord_post($item_id, $title, $start, $duration, $room_id, $post_url) {
  global $mysqli;

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

  $stmt = $mysqli->prepare("INSERT INTO prog_discord_posts (item_id, title, start, duration, room_id, post_url) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE start = VALUES(start), duration = VALUES(duration), room_id = VALUES(room_id), post_url = VALUES(post_url)");
  $stmt->bind_param("ssiiss", $item_id, $title, $start_time, $duration_secs, $room_id, $post_url);
  $stmt->execute();
  $stmt->close();
}

function db_delete_discord_post($item_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("DELETE FROM prog_discord_posts WHERE item_id = ?");
  $stmt->bind_param("s", $item_id);
  $stmt->execute();
  $stmt->close();
}

function db_get_zoom_url() {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT zoom_url FROM prog_zoom");
  $stmt->execute();
  $stmt->bind_result($url);
  $stmt->fetch();
  $stmt->close();

  return $url;
}

function db_set_zoom_url($url) {
  global $mysqli;

  $stmt = $mysqli->prepare("UPDATE prog_zoom SET zoom_url = ?");
  $stmt->bind_param("s", $url);
  $stmt->execute();
  $stmt->close();
}

function db_get_replays() {
  global $mysqli;

  $result = $mysqli->query("SELECT item_id, title FROM prog_replay ORDER BY title, item_id");
  $items = [];
  while ($row = $result->fetch_assoc()) {
    $items[] = $row;
  }
  $result->close();

  return $items;
}

function db_get_replay($item_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("SELECT title, replay_url FROM prog_replay WHERE item_id = ?");
  $stmt->bind_param("s", $item_id);
  $stmt->execute();
  $stmt->bind_result($title, $replay_url);
  $stmt->fetch();
  $stmt->close();

  return [
    'title' => $title,
    'replay_url' => $replay_url,
  ];
}

function db_edit_replay($item_id, $title, $replay_url) {
  global $mysqli;

  $stmt = $mysqli->prepare("UPDATE prog_replay SET title = ?, replay_url = ? WHERE item_id = ?");
  $stmt->bind_param("sss", $title, $replay_url, $item_id);
  $stmt->execute();
  $stmt->close();
}

function db_add_replay($item_id, $title, $replay_url) {
  global $mysqli;

  $stmt = $mysqli->prepare("INSERT INTO prog_replay (item_id, title, replay_url) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $item_id, $title, $replay_url);
  $stmt->execute();
  $stmt->close();

  return $mysqli->insert_id;
}

function db_delete_replay($item_id) {
  global $mysqli;

  $stmt = $mysqli->prepare("DELETE FROM prog_replay WHERE item_id = ?");
  $stmt->bind_param("s", $item_id);
  $stmt->execute();
  $stmt->close();
}

function db_insert_discord_token($token, $discord_id, $discord_username) {
  global $mysqli;

  $stmt = $mysqli->prepare("INSERT INTO discord_verification_token (token_id, discord_id, username) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $token, $discord_id, $discord_username);
  $stmt->execute();
  $stmt->close();
}

function db_set_discord_id_from_token($badge_no, $token) {
  global $mysqli;

  $mysqli->begin_transaction();
  try {
    $stmt = $mysqli->prepare("SELECT discord_id, username, verified_at FROM discord_verification_token WHERE token_id = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($discord_id, $discord_username, $verified_at);
    if (!$stmt->fetch()) {
      return [ "result"=>"no-token" ];
    }
    $stmt->close();

    if ($verified_at) {
      return [ "result"=>"already-verified", "id"=>$discord_id, "username"=>$discord_username ];
    }

    db_set_discord_id($badge_no, $discord_id, $discord_username);

    $stmt = $mysqli->prepare("UPDATE discord_verification_token SET verified_at = CURRENT_TIMESTAMP() WHERE token_id = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();

    $mysqli->commit();

    return [ "result"=>"verified", "id"=>$discord_id, "username"=>$discord_username ];
  } catch (mysqli_sql_exception $exception) {
    $mysqli->rollback();
    throw $exception;
  }
}
?>