<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
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

// Each member with the actions the desk can take on them, each a form
// confirmed in a dialog naming them
$rows = [];
foreach ($members as $member) {
  $selection = $selections[(string) $member['badge_no']] ?? null;
  $actions = [];
  if ($selection === null) {
    $status = "Hasn't paid";
    $actions[] = ['action' => 'sell', 'label' => 'Sell paper ballot', 'title' => 'Have you taken payment?', 'message' => 'Take the site selection fee, then give them a paper ballot.'];
  } else if ($selection['method'] === 'online') {
    $status = 'Can vote online';
    $actions[] = ['action' => 'transfer', 'label' => 'Move to paper ballot', 'title' => 'Move to a paper ballot?', 'message' => 'This spoils their online key. Give them a paper ballot without taking payment again.'];
    if ($can_manage) {
      $actions[] = ['action' => 'replace', 'label' => 'Give new online key', 'title' => 'Give a new online key?', 'message' => 'This spoils their online key and gives them a new voting link on the site selection page.'];
    }
  } else {
    $status = 'Voting on paper';
    if ($can_manage) {
      $actions[] = ['action' => 'unassign', 'label' => 'Unassign paper ballot', 'title' => 'Unassign their paper ballot?', 'message' => 'Use this only if the ballot was sold by mistake. They will be able to pay again.'];
    }
  }
  $rows[] = [
    'badge_no' => (string) $member['badge_no'],
    'name' => (string) ($member['name'] ?? ''),
    'email' => $member['email'] ?? '',
    'status' => $status,
    'actions' => $actions,
  ];
}

render_page('pages/admin/site-selection/desk.html.twig', [
  'title' => 'Site selection desk',
  'description' => 'Sell site selection paper ballots.',
  'breadcrumbs' => ['Home' => '/', 'Site selection desk'],
  'message' => $message,
  'message_is_error' => in_array($message_key, $errors, true),
  'search' => $search,
  'page' => $page,
  'total' => $total,
  'first' => ($page - 1) * PER_PAGE + 1,
  'may_be_more' => $total > 0 && $total >= MEMBER_SOURCE_SEARCH_LIMIT,
  'rows' => $rows,
  'pages' => pagination_links($page, $total_pages, ['search' => $search]),
]);
