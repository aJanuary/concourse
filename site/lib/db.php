<?php

require_once('config.php');

function db_connect() {
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  return new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
}

function db_check_member_creds($email, $password) {
  $mysqli = db_connect();

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

function db_insert_session($session_token, $email, $expires_at) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("INSERT INTO sessions (session_id, expires_at, badge_no) SELECT ?, ?, badge_no FROM members WHERE email = ?");
  $expires_at_ts = date('Y-m-d H:i:s', $expires_at);
  $stmt->bind_param("sss", $session_token, $expires_at_ts, $email);
  $stmt->execute();
  $stmt->close();
}

function db_session_exists($session_token) {
  $mysqli = db_connect();

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
  $mysqli = db_connect();

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
  $mysqli = db_connect();

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
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("DELETE FROM sessions WHERE session_id = ?");
  $stmt->bind_param("s", $session_token);
  $stmt->execute();
  $stmt->close();
}

function db_create_oauth_identity($badge_id, $provider,  $identity_id, $email, $raw_info) {
  $mysqli = db_connect();

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
  $mysqli = db_connect();

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
  $mysqli = db_connect();

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
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("REPLACE INTO discord_ids(badge_no, discord_id, username) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $badge_no, $discord_id, $discord_username);
  $stmt->execute();
  $stmt->close();
}

function db_identity_exists($email) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT 1 FROM oauth_identities WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->bind_result($exists);
  $stmt->fetch();
  $stmt->close();

  return $exists;
}

function db_member_exists($email) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT 1 FROM members WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->bind_result($exists);
  $stmt->fetch();
  $stmt->close();

  return $exists;
}

function db_validate_member_identity($email, $provider) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT 1 FROM members left join oauth_identities on oauth_identities.email = members.email and oauth_identities.provider = ? WHERE members.email = ? and oauth_identities.badge_no = members.badge_no");
  $stmt->bind_param("ss", $provider, $email);
  $stmt->execute();
  $stmt->bind_result($exists);
  $stmt->fetch();
  $stmt->close();

  return $exists;
}

function db_insert_login_link($email, $login_code, $expires_at) {
  $mysqli = db_connect();

  $expires_at_ts = date('Y-m-d H:i:s', $expires_at);
  $stmt = $mysqli->prepare("INSERT INTO login_links (login_code, badge_no, expires_at) SELECT ?, badge_no, ? FROM members WHERE email = ?");
  $stmt->bind_param("sss", $login_code, $expires_at_ts, $email);
  $stmt->execute();
  $stmt->close();
}

function db_get_login_link_expiry($email, $login_code) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT expires_at FROM login_links WHERE login_code = ? AND badge_no = (SELECT badge_no FROM members WHERE email = ?)");
  $stmt->bind_param("ss", $login_code, $email);
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
  $mysqli = db_connect();

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
  $mysqli = db_connect();

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
  $mysqli = db_connect();

  $result = $mysqli->query("SELECT members.badge_no, name, invite_url from members LEFT OUTER JOIN rce_invites USING (badge_no) ORDER by badge_no");
  $members = [];
  while ($row = $result->fetch_assoc()) {
    $members[] = ['badge_no' => $row['badge_no'], 'name' => $row['name'], 'invite_url' => $row['invite_url']];
  }
  $result->close();

  return $members;
}

function db_get_discord_usernames($session_id) {
  $mysqli = db_connect();

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
  $mysqli = db_connect();

  $result = $mysqli->query("SELECT role_id, name FROM roles");
  $roles = [];
  while ($row = $result->fetch_assoc()) {
    $roles[] = $row;
  }
  $result->close();

  return $roles;
}

function db_get_role_permissions($role_id) {
  $mysqli = db_connect();

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
  $mysqli = db_connect();

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
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("INSERT INTO roles (name) VALUES (?)");
  $stmt->bind_param("s", $name);
  $stmt->execute();
  $stmt->close();

  return $mysqli->insert_id;
}

function db_delete_role($role_id) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("DELETE FROM roles WHERE role_id = ?");
  $stmt->bind_param("s", $role_id);
  $stmt->execute();
  $stmt->close();
}

function db_get_permissions_by_session($session_id) {
  if (db_session_has_admin_role($session_id)) {
    return db_get_permissions();
  }

  $mysqli = db_connect();

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

function db_session_has_admin_role($session_id) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT 1 FROM member_roles JOIN sessions USING (badge_no) JOIN roles USING (role_id) WHERE session_id = ? AND roles.name = 'admin'");
  $stmt->bind_param("s", $session_id);
  $stmt->execute();
  $stmt->bind_result($has_admin_role);
  $stmt->fetch();
  $stmt->close();

  return $has_admin_role;
}

function db_get_permissions() {
  $mysqli = db_connect();

  $result = $mysqli->query("SELECT name FROM permissions");
  $permissions = [];
  while ($row = $result->fetch_assoc()) {
    $permissions[] = $row['name'];
  }
  $result->close();

  return $permissions;
}

function db_get_role_id($role_name) {
  $mysqli = db_connect();

  $stmt = $mysqli->prepare("SELECT role_id FROM roles WHERE name = ?");
  $stmt->bind_param("s", $role_name);
  $stmt->execute();
  $stmt->bind_result($role_id);
  $stmt->fetch();
  $stmt->close();

  return $role_id;
}

?>