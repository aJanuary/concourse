<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

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

render_header(
  'Edit member roles',
  'Give or remove roles for a member.',
  ['Home' => '/', 'Manage roles' => '/admin/roles', 'Edit member roles']
);
?>
  <article>
    <h2>Edit member roles</h2>
<?php
if ($message !== null) {
  if ($message_class === 'error') {
?>
    <p class="error"><img src="/resources/error.svg" alt="error" aria-label="error"><?php echo htmlspecialchars($message); ?></p>
<?php
  } else {
?>
    <p class="message" role="status"><?php echo htmlspecialchars($message); ?></p>
<?php
  }
}

if ($member === null) {
?>
    <form action="/admin/roles/edit-member-roles" method="GET">
      <p><label>Badge number, name or email: <input name="q" required value="<?php echo htmlspecialchars($query); ?>"></label></p>
      <input type="submit" value="Find member">
    </form>
<?php
  if ($results !== null) {
    if (!$results) {
?>
    <p>No members match.</p>
<?php
    } else {
?>
    <ul>
<?php
      foreach ($results as $result) {
?>
      <li><a href="<?php echo htmlspecialchars(member_roles_url(['badge_no' => $result['badge_no']])); ?>"><?php echo htmlspecialchars($result['name']); ?></a> (<?php echo htmlspecialchars($result['badge_no']); ?>, <?php echo htmlspecialchars($result['email']); ?>)</li>
<?php
      }
?>
    </ul>
<?php
    }
  }
} else {
?>
    <p>
      <?php echo htmlspecialchars($member['name']); ?><?php echo $member['real_name'] !== null ? ' (' . htmlspecialchars($member['real_name']) . ')' : ''; ?><br>
      Badge number <?php echo htmlspecialchars($badge_no); ?>, <?php echo htmlspecialchars($member['email']); ?>
    </p>
    <h3>Roles</h3>
<?php
  if (!$member_roles) {
?>
    <p>They have no roles beyond the default.</p>
<?php
  } else {
?>
    <ul>
<?php
    foreach ($member_roles as $role) {
      $note = role_login_sync_note($badge_no, $role['name']);
?>
      <li>
        <form action="/admin/roles/edit-member-roles" method="POST">
          <?php echo htmlspecialchars($role['name']); ?>
          <input type="hidden" name="action" value="remove">
          <input type="hidden" name="badge_no" value="<?php echo htmlspecialchars($badge_no); ?>">
          <input type="hidden" name="role_id" value="<?php echo htmlspecialchars((string) $role['role_id']); ?>">
          <input type="submit" value="Remove" aria-label="Remove <?php echo htmlspecialchars($role['name']); ?>">
<?php
      if ($note !== null) {
?>
          <br><small><?php echo htmlspecialchars($note); ?>, so removing it here may be undone.</small>
<?php
      }
?>
        </form>
      </li>
<?php
    }
?>
    </ul>
<?php
  }

  if ($other_roles) {
?>
    <form action="/admin/roles/edit-member-roles" method="POST">
      <input type="hidden" name="action" value="add">
      <input type="hidden" name="badge_no" value="<?php echo htmlspecialchars($badge_no); ?>">
      <label>Add role: <select name="role_id">
<?php
    foreach ($other_roles as $role) {
      $note = role_login_sync_note($badge_no, $role['name']);
?>
        <option value="<?php echo htmlspecialchars((string) $role['role_id']); ?>"><?php echo htmlspecialchars($role['name']); ?><?php echo $note !== null ? ' (' . htmlspecialchars(strtolower($note)) . ')' : ''; ?></option>
<?php
    }
?>
      </select></label>
      <input type="submit" value="Add">
    </form>
<?php
  }
?>
    <p>Changes take effect on the member's next page load.</p>
    <p><a href="/admin/roles/edit-member-roles">Find another member</a></p>
<?php
}
?>
  </article>
<?php
render_footer();
?>
