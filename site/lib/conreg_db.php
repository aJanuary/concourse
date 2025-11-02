<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conreg_mysqli = new mysqli("p:" . DB_HOST, DB_USER, DB_PASSWORD, CONREG_DB_NAME);

function conreg_is_allowed($badge_no) {
  if (!defined('CONREG_ALLOWLIST')) {
    return true;
  }
  error_log("Looking for " . $badge_no . " in:");
  error_log(print_r(CONREG_ALLOWLIST, true));
  error_log(print_r(debug_backtrace(), true)); 
  return in_array($badge_no, CONREG_ALLOWLIST);
}

function conreg_get_members_from_email($email) {
  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT member_no as badge_no, badge_name as name FROM conreg_members WHERE is_approved = 1 AND (email = ? OR (email = '' AND lead_mid IN (SELECT mid FROM conreg_members WHERE email = ?)))");

  try {
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $stmt->bind_result($badge_no, $name);
    $members = [];
    while ($stmt->fetch()) {
      if (conreg_is_allowed($badge_no)) {
        $members[] = ['badge_no' => $badge_no, 'name' => $name];
      }
    }
  } finally {
    $stmt->close();
  }

  return $members;
}

function conreg_user_owns_multiple_members($badge_no) {
  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT member_no as badge_no FROM conreg_members WHERE is_approved=1 AND (member_no = ? OR (lead_mid IN (SELECT lead_mid FROM conreg_members WHERE member_no = ? AND (mid = lead_mid OR email = '')) AND (mid = lead_mid OR email = '')))");
  try {
    $stmt->bind_param("ss", $badge_no, $badge_no);
    $stmt->execute();
    $stmt->bind_result($badge_no);
    $count = 0;
    while ($stmt->fetch()) {
      if (conreg_is_allowed($badge_no)) {
        $count += 1;
      }
    }
  } finally {
    $stmt->close();
  }

  return $count > 1;
}

function conreg_get_members_owned_by_same_user($badge_no) {
  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT member_no as badge_no, badge_name as name, email FROM conreg_members WHERE is_approve = 1 AND (member_no = ? OR (lead_mid IN (SELECT lead_mid FROM conreg_members WHERE member_no = ? AND (mid = lead_mid OR email = '')) AND (mid = lead_mid OR email = '')))");
  try {
    $stmt->bind_param("ss", $badge_no, $badge_no);
    $stmt->execute();
    $stmt->bind_result($b_no, $name, $email);
    $members = [];
    while ($stmt->fetch()) {
      if (conreg_is_allowed($badge_no)) {
        $members[] = ['badge_no' => $b_no, 'name' => $name, 'email' => $email];
      }
    }
  } finally {
    $stmt->close();
  }

  return $members;
}

function conreg_get_member_by_badge_no($badge_no) {
  if (!conreg_is_allowed($badge_no)) {
    return null;
  }

  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT cur.badge_name as name, COALESCE(NULLIF(cur.email, ''), `lead`.email) as email FROM conreg_members cur JOIN conreg_members `lead` ON cur.lead_mid = `lead`.mid WHERE cur.is_approved = 1 AND cur.member_no = ?");
  try {
    $stmt->bind_param("i", $badge_no);
    $stmt->execute();
    $stmt->bind_result($name, $email);
    if ($stmt->fetch()) {
      return ['badge_no' => $badge_no, 'name' => $name, 'email' => $email];
    } else {
      return null;
    }
  } finally {
    $stmt->close();
  }
}


function conreg_any_member_has_email($email) {
  global $conreg_mysqli;

  $stmt = $conreg_mysqli->prepare("SELECT member_no as badge_no FROM conreg_members WHERE is_approved = 1 AND (email = ? OR (email = '' AND lead_mid IN (SELECT mid FROM conreg_members WHERE email = ?)))");

  try {
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $stmt->bind_result($badge_no);
    $members = [];
    while ($stmt->fetch()) {
      if (conreg_is_allowed($badge_no)) {
        return true;
      }
    }
  } finally {
    $stmt->close();
  }

  return false;
}

?>