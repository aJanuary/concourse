<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

check_permission('manage-roles');

const MEMBER_ROLE_MESSAGES = [
  'added' => ['message', 'Added the %s role.'],
  'removed' => ['message', 'Removed the %s role.'],
  'already-had' => ['error', 'They already have the %s role.'],
  'did-not-have' => ['error', 'They do not have the %s role.'],
  'own-access' => ['error', 'You cannot remove the %s role from yourself, because you would lose access to this page.'],
  'no-such-role' => ['error', 'That role no longer exists.'],
  'no-such-member' => ['error', 'There is no member with that badge number.'],
];

function member_roles_url($params) {
  return '/admin/roles/edit-member-roles?' . http_build_query($params);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;
  $badge_no = trim((string) ($_POST['badge_no'] ?? ''));
  $role = db_get_role((int) ($_POST['role_id'] ?? 0));

  if (!db_member_exists($badge_no)) {
    $status = 'no-such-member';
  } else if ($role === null) {
    $status = 'no-such-role';
  } else if ($action === 'add') {
    $status = db_grant_member_role($badge_no, $role['role_id']) ? 'added' : 'already-had';
  } else if ($action === 'remove') {
    if ($badge_no === get_current_user_badge_no() && !in_array('manage-roles', db_get_member_permissions_without_role($badge_no, $role['role_id']), true)) {
      $status = 'own-access';
    } else {
      $status = db_revoke_member_role($badge_no, $role['role_id']) ? 'removed' : 'did-not-have';
    }
  } else {
    header('Location: /admin/roles/edit-member-roles');
    exit;
  }

  $params = ['status' => $status];
  if ($status !== 'no-such-member') {
    $params['badge_no'] = $badge_no;
  }
  if ($role !== null) {
    $params['role_id'] = $role['role_id'];
  }
  header('Location: ' . member_roles_url($params));
  exit;
}

$query = trim((string) ($_GET['q'] ?? ''));
$badge_no = trim((string) ($_GET['badge_no'] ?? ''));

$message = null;
if (isset(MEMBER_ROLE_MESSAGES[$_GET['status'] ?? ''])) {
  [$message_class, $message_format] = MEMBER_ROLE_MESSAGES[$_GET['status']];
  $message_role = db_get_role((int) ($_GET['role_id'] ?? 0));
  $message = sprintf($message_format, $message_role['name'] ?? 'selected');
}

$member = null;
if ($badge_no !== '') {
  if (db_member_exists($badge_no)) {
    $member = db_get_member($badge_no);
    $member_roles = db_get_member_roles($badge_no);
    $member_role_ids = array_column($member_roles, 'role_id');
    $other_roles = array_filter(db_get_roles(), function ($role) use ($member_role_ids) {
      // Everyone has the default role's permissions already
      return $role['name'] !== 'default' && !in_array($role['role_id'], $member_role_ids);
    });
    usort($other_roles, function ($a, $b) {
      return strcasecmp($a['name'], $b['name']);
    });
  } else {
    $message_class = 'error';
    $message = 'There is no member with badge number ' . $badge_no . '.';
  }
}

$results = ($member === null && $query !== '') ? db_search_members($query) : null;

// The member's roles, and the ones they could be given, each with a note
// when logging in sets it
$roles = [];
$addable_roles = [];
if ($member !== null) {
  foreach ($member_roles as $role) {
    $roles[] = $role + ['note' => role_login_sync_note($badge_no, $role['name'])];
  }
  foreach ($other_roles as $role) {
    $addable_roles[] = $role + ['note' => role_login_sync_note($badge_no, $role['name'])];
  }
}

render_page('pages/admin/roles/edit-member-roles.html.twig', [
  'message' => $message,
  'message_class' => $message_class ?? null,
  'query' => $query,
  'results' => $results,
  'badge_no' => $badge_no,
  'member' => $member,
  'roles' => $roles,
  'addable_roles' => $addable_roles,
]);
