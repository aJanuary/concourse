<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');

// The site selection desk at the convention: find a member, take their fee
// and give them a paper ballot, or move a member who paid online to paper.
// Members are found through the member source, as they may not have logged in.
check_permission('sell-tokens');

const PER_PAGE = 20;

$messages = [
  'sold' => 'Sold a paper ballot to %s.',
  'transferred' => 'Moved %s to a paper ballot. Their online key is spoilt.',
  'replaced' => 'Gave %s a new online key. Their old one is spoilt.',
  'unassigned' => 'Unassigned %s.',
  'already_has_token' => '%s can already vote, so nothing was changed.',
  'no_online_token' => "%s doesn't have an online key, so nothing was changed.",
  'no_tokens' => 'There are no voter keys left to give %s. Add more under Manage site selection.',
  'not_found' => "Couldn't find member %s.",
];
$errors = ['already_has_token', 'no_online_token', 'no_tokens', 'not_found'];

function find_member($badge_no) {
  foreach (auth_member_source()->search($badge_no) as $member) {
    if ((string) $member['badge_no'] === $badge_no) {
      return $member;
    }
  }
  return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  $badge_no = trim((string) ($_POST['badge_no'] ?? ''));
  $staff = get_current_user_badge_no();
  $member = $badge_no === '' ? null : find_member($badge_no);

  if ($member === null && !db_member_exists($badge_no)) {
    $result = ['success' => false, 'error' => 'not_found'];
  } else if ($action === 'sell') {
    if ($member !== null) {
      site_selection_ensure_member($member);
    }
    $result = db_sell_paper_site_selection_ballot($badge_no, $staff) + ['done' => 'sold'];
  } else if ($action === 'transfer') {
    $result = db_transfer_site_selection_to_paper($badge_no, $staff) + ['done' => 'transferred'];
  } else if ($action === 'replace') {
    check_permission('manage-tokens');
    $result = db_replace_site_selection_token($badge_no, $staff) + ['done' => 'replaced'];
  } else if ($action === 'unassign') {
    check_permission('manage-tokens');
    $current = db_get_member_site_selection($badge_no);
    if ($current !== null) {
      db_unassign_site_selection_token($current['id']);
    }
    $result = ['success' => true, 'done' => 'unassigned'];
  } else {
    render_404();
  }

  $params = array_filter([
    'search' => trim((string) ($_POST['search'] ?? '')),
    'page' => max(1, (int) ($_POST['page'] ?? 1)) > 1 ? (int) $_POST['page'] : null,
    'message' => $result['success'] ? $result['done'] : $result['error'],
    'badge_no' => $badge_no,
  ]);
  header('Location: /admin/site-selection/desk?' . http_build_query($params));
  exit;
}

$search = trim((string) ($_GET['search'] ?? ''));
$page = current_page_number();
$message_key = $_GET['message'] ?? '';
$message = isset($messages[$message_key]) ? sprintf($messages[$message_key], 'badge ' . (string) ($_GET['badge_no'] ?? '')) : null;

$members = [];
$total = 0;
if ($search !== '') {
  $all = auth_member_source()->search($search);
  usort($all, fn($a, $b) => strnatcasecmp((string) $a['badge_no'], (string) $b['badge_no']));
  $total = count($all);
  $members = array_slice($all, ($page - 1) * PER_PAGE, PER_PAGE);
}
$total_pages = max(1, (int) ceil($total / PER_PAGE));
$selections = db_get_members_site_selections(array_map(fn($member) => (string) $member['badge_no'], $members));
$can_manage = current_user_has_permission('manage-tokens');

// A form for one action on one member, confirmed in a dialog naming them
function action_form($action, $label, $member, $title, $message) {
  global $search, $page;
  $details = ['Action' => $label, 'Badge number' => (string) $member['badge_no'], 'Name' => (string) ($member['name'] ?? '')];
?>
            <form method="POST"
              data-confirm-title="<?php echo htmlspecialchars($title); ?>"
              data-confirm-details="<?php echo htmlspecialchars(json_encode($details)); ?>"
              data-confirm-message="<?php echo htmlspecialchars($message); ?>"
              data-confirm-button="<?php echo htmlspecialchars($label); ?>">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="action" value="<?php echo htmlspecialchars($action); ?>">
              <input type="hidden" name="badge_no" value="<?php echo htmlspecialchars($member['badge_no']); ?>">
              <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
              <input type="hidden" name="page" value="<?php echo $page; ?>">
              <input type="submit" value="<?php echo htmlspecialchars($label); ?>">
            </form>
<?php
}

render_header(
  'Site selection desk',
  'Sell site selection paper ballots.',
  ['Home' => '/', 'Site selection desk']
);
?>
<article class="full-width">
  <h2>Site selection desk</h2>
<?php
if ($message !== null) {
  if (in_array($message_key, $errors, true)) {
?>
  <div class="error" role="alert">
    <img src="/resources/error.svg" alt="error" aria-label="error">
    <?php echo htmlspecialchars($message); ?>
  </div>
<?php
  } else {
?>
  <p class="info" role="status"><?php echo htmlspecialchars($message); ?></p>
<?php
  }
}
?>
  <form method="GET" class="inline-actions">
    <label for="search">Badge number, name or email</label>
    <input type="search" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" required<?php echo $members ? '' : ' autofocus'; ?>>
    <input type="submit" value="Search">
<?php
if ($search !== '') {
?>
    <a href="/admin/site-selection/desk">Clear</a>
<?php
}
?>
  </form>
<?php
if ($search !== '' && $total === 0) {
?>
  <p>No members match &ldquo;<?php echo htmlspecialchars($search); ?>&rdquo;.</p>
<?php
} else if ($total > 0) {
  $first = ($page - 1) * PER_PAGE + 1;
?>
  <p>Showing <?php echo $first; ?>&ndash;<?php echo $first + count($members) - 1; ?> of <?php echo $total; ?> members<?php echo $total >= MEMBER_SOURCE_SEARCH_LIMIT ? '. There may be more: search for something more specific' : ''; ?>.</p>
  <table>
    <thead>
      <tr><th>Badge number</th><th>Name</th><th>Email</th><th>Site selection</th></tr>
    </thead>
    <tbody>
<?php
  foreach ($members as $member) {
    $selection = $selections[(string) $member['badge_no']] ?? null;
?>
      <tr>
        <td><?php echo htmlspecialchars($member['badge_no']); ?></td>
        <td><?php echo htmlspecialchars($member['name'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($member['email'] ?? ''); ?></td>
        <td>
          <div class="inline-actions">
<?php
    if ($selection === null) {
?>
            <span>Hasn't paid</span>
<?php
      action_form('sell', 'Sell paper ballot', $member, 'Have you taken payment?', 'Take the site selection fee, then give them a paper ballot.');
    } else if ($selection['method'] === 'online') {
?>
            <span>Can vote online</span>
<?php
      action_form('transfer', 'Move to paper ballot', $member, 'Move to a paper ballot?', 'This spoils their online key. Give them a paper ballot without taking payment again.');
      if ($can_manage) {
        action_form('replace', 'Give new online key', $member, 'Give a new online key?', 'This spoils their online key and gives them a new voting link on the site selection page.');
      }
    } else {
?>
            <span>Voting on paper</span>
<?php
      if ($can_manage) {
        action_form('unassign', 'Unassign paper ballot', $member, 'Unassign their paper ballot?', 'Use this only if the ballot was sold by mistake. They will be able to pay again.');
      }
    }
?>
          </div>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>
<?php
  render_pagination($page, $total_pages, ['search' => $search]);
}
?>
</article>
<script src="<?php echo get_internal_url('/resources/confirm.js'); ?>" defer></script>
<?php
render_footer();
?>
