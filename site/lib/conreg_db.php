<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conreg_mysqli = new mysqli("p:" . DB_HOST, DB_USER, DB_PASSWORD, CONREG_DB_NAME);

function conreg_is_allowed($badge_no, $badge_type) {
  if ($badge_type == 'C') {
    return true;
  }

  if (!defined('CONREG_ALLOWLIST')) {
    return true;
  }
  return in_array($badge_no, CONREG_ALLOWLIST);
}

function conreg_get_members_from_email($email) {
  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT member_no as badge_no, badge_name as name, badge_type FROM conreg_members WHERE is_approved = 1 AND (email = ? OR (email = '' AND lead_mid IN (SELECT mid FROM conreg_members WHERE email = ?)))");

  try {
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $stmt->bind_result($badge_no, $name, $badge_type);
    $members = [];
    while ($stmt->fetch()) {
      if (conreg_is_allowed($badge_no, $badge_type)) {
        $members[] = ['badge_no' => $badge_no, 'name' => $name];
      }
    }
  } finally {
    $stmt->close();
  }

  if (defined('CONREG_HARDCODED_MEMBERS')) {
    foreach (CONREG_HARDCODED_MEMBERS as $bypass_entry) {
      if (strcasecmp($bypass_entry['email'], $email) === 0) {
        $members[] = [
          'badge_no' => $bypass_entry['badge_no'], 
          'name' => $bypass_entry['name']
        ];
      }
    }
  }

  return $members;
}

function conreg_user_owns_multiple_members($badge_no) {
  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT member_no as badge_no, badge_type FROM conreg_members WHERE is_approved=1 AND (member_no = ? OR (lead_mid IN (SELECT lead_mid FROM conreg_members WHERE member_no = ? AND (mid = lead_mid OR email = '')) AND (mid = lead_mid OR email = '')))");
  try {
    $stmt->bind_param("ii", $badge_no, $badge_no);
    $stmt->execute();
    $stmt->bind_result($badge_no, $badge_type);
    $count = 0;
    while ($stmt->fetch()) {
      if (conreg_is_allowed($badge_no, $badge_type)) {
        $count += 1;
      }
    }
  } finally {
    $stmt->close();
  }

  if (defined('CONREG_HARDCODED_MEMBERS')) {
    foreach (CONREG_HARDCODED_MEMBERS as $bypass_entry) {
      if ($bypass_entry['badge_no'] === $badge_no) {
        $count += 1;
      }
    }
  }

  return $count > 1;
}

function conreg_get_members_owned_by_same_user($badge_no) {
  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT member_no as badge_no, badge_name as name, email, badge_type FROM conreg_members WHERE is_approved = 1 AND (member_no = ? OR (lead_mid IN (SELECT lead_mid FROM conreg_members WHERE member_no = ? AND (mid = lead_mid OR email = '')) AND (mid = lead_mid OR email = '')))");
  try {
    $stmt->bind_param("ii", $badge_no, $badge_no);
    $stmt->execute();
    $stmt->bind_result($b_no, $name, $email, $badge_type);
    $members = [];
    while ($stmt->fetch()) {
      if (conreg_is_allowed($badge_no, $badge_type)) {
        $members[] = ['badge_no' => $b_no, 'name' => $name, 'email' => $email];
      }
    }
  } finally {
    $stmt->close();
  }

  if (defined('CONREG_HARDCODED_MEMBERS')) {
    foreach (CONREG_HARDCODED_MEMBERS as $bypass_entry) {
      if ($bypass_entry['badge_no'] === $badge_no) {
        $members[] = [
          'badge_no' => $bypass_entry['badge_no'], 
          'name' => $bypass_entry['name'],
          'email' => $bypass_entry['email']
        ];
      }
    }
  }


  return $members;
}

function conreg_roles_for_badge_type($badge_type) {
  $roles = [];
  if ($badge_type == 'S') {
    $roles[] = 'online';
  } else {
    $roles[] = 'in-person';
  }
  return $roles;
}

function conreg_get_member_by_badge_no($badge_no) {
  if (defined('CONREG_HARDCODED_MEMBERS')) {
    foreach (CONREG_HARDCODED_MEMBERS as $bypass_entry) {
      if ($bypass_entry['badge_no'] === $badge_no) {
        return [
          'badge_no' => $bypass_entry['badge_no'], 
          'name' => $bypass_entry['name'],
          'email' => $bypass_entry['email'],
          'roles' => conreg_roles_for_badge_type($bypass_entry['member_type'])
        ];
      }
    }
  }

  
  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT cur.badge_name as name, COALESCE(NULLIF(cur.email, ''), `lead`.email) as email, cur.badge_type FROM conreg_members cur JOIN conreg_members `lead` ON cur.lead_mid = `lead`.mid WHERE cur.is_approved = 1 AND cur.member_no = ?");
  try {
    $stmt->bind_param("i", $badge_no);
    $stmt->execute();
    $stmt->bind_result($name, $email, $badge_type);
    if ($stmt->fetch()) {
      if (!conreg_is_allowed($badge_no, $badge_type)) {
        return null;
      }

      $roles = conreg_roles_for_badge_type($badge_type);
      return ['badge_no' => $badge_no, 'name' => $name, 'email' => $email, 'roles' => $roles];
    } else {
      return null;
    }
  } finally {
    $stmt->close();
  }
}


function conreg_any_member_has_email($email) {
  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT member_no as badge_no, badge_type FROM conreg_members WHERE is_approved = 1 AND (email = ? OR (email = '' AND lead_mid IN (SELECT mid FROM conreg_members WHERE email = ?)))");

  try {
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $stmt->bind_result($badge_no, $badge_type);
    $members = [];
    while ($stmt->fetch()) {
      if (conreg_is_allowed($badge_no, $badge_type)) {
        return true;
      }
    }
  } finally {
    $stmt->close();
  }

  if (defined('CONREG_HARDCODED_MEMBERS')) {
    foreach (CONREG_HARDCODED_MEMBERS as $bypass_entry) {
      if (strcasecmp($bypass_entry['email'], $email) === 0) {
        return true;
      }
    }
  }

  return false;
}

?>