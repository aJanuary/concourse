<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');

check_permission('manage-tokens');

const PER_PAGE = 50;

$search = trim((string) ($_GET['search'] ?? ''));
$sort = array_key_exists($_GET['sort'] ?? '', DB_SITE_SELECTION_TOKEN_SORTS) ? $_GET['sort'] : 'added_at';
$ascending = ($_GET['dir'] ?? '') === 'asc';
$page = current_page_number();

// The list's query parameters, less those left at their defaults
$list_params = array_filter([
  'search' => $search,
  'sort' => $sort === 'added_at' ? '' : $sort,
  'dir' => $ascending ? 'asc' : '',
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $ids = array_map('intval', (array) ($_POST['ids'] ?? []));
  $action = $_POST['bulk_action'] ?? '';
  $done = ['done' => $action, 'count' => 0];
  if ($action === 'unassign') {
    foreach ($ids as $id) {
      db_unassign_site_selection_token($id);
    }
    $done['count'] = count($ids);
  } else if ($action === 'delete') {
    foreach ($ids as $id) {
      if (db_delete_site_selection_token($id)) {
        $done['count']++;
      } else {
        $done['skipped'] = ($done['skipped'] ?? 0) + 1;
      }
    }
  }
  header('Location: /admin/site-selection/list?' . http_build_query($list_params + ($page > 1 ? ['page' => $page] : []) + $done));
  exit;
}

function plural($count, $one, $many) {
  return $count === 1 ? "1 $one" : "$count $many";
}

$message = null;
$count = (int) ($_GET['count'] ?? 0);
if (isset($_GET['added'])) {
  $message = 'Added ' . plural((int) $_GET['added'], 'key', 'keys') . '.';
} else if (($_GET['done'] ?? '') === 'unassign') {
  $message = 'Unassigned ' . plural($count, 'key', 'keys') . '.';
} else if (($_GET['done'] ?? '') === 'delete') {
  $message = 'Deleted ' . plural($count, 'key', 'keys') . '.';
  $skipped = (int) ($_GET['skipped'] ?? 0);
  if ($skipped > 0) {
    $message .= ' ' . plural($skipped, 'key was', 'keys were') . ' assigned, so not deleted. Unassign them first.';
  }
}

$total = db_count_site_selection_tokens($search);
$total_pages = max(1, (int) ceil($total / PER_PAGE));
$tokens = db_get_site_selection_tokens(PER_PAGE, ($page - 1) * PER_PAGE, $search, $sort, !$ascending);
$available = db_count_available_site_selection_tokens();

// The table's columns, each a link that sorts by it, or reverses the sort
$columns = [];
foreach (['label' => 'Label', 'token' => 'Key', 'badge_no' => 'Status', 'added_by' => 'Added by', 'added_at' => 'Added'] as $column => $heading) {
  $ascending_next = !($sort === $column && $ascending);
  $columns[] = [
    'heading' => $heading . ($sort === $column ? ($ascending ? ' ▲' : ' ▼') : ''),
    'url' => '?' . http_build_query(array_filter(['search' => $search, 'sort' => $column, 'dir' => $ascending_next ? 'asc' : 'desc'])),
    'aria_sort' => $sort === $column ? ($ascending ? 'ascending' : 'descending') : null,
  ];
}

function token_status($token) {
  if ($token['badge_no'] === null) {
    return 'Available';
  }
  $who = $token['badge_no'] . ($token['name'] !== null ? ' (' . $token['name'] . ')' : '');
  return ($token['spoilt_at'] !== null ? 'Spoilt, was ' : 'Assigned to ') . $who;
}

$rows = [];
foreach ($tokens as $token) {
  $rows[] = [
    'id' => $token['id'],
    'label' => $token['label'],
    'token' => $token['token'],
    'status' => token_status($token),
    'added_by' => $token['added_by'] . ($token['added_by_name'] !== null ? ' (' . $token['added_by_name'] . ')' : ''),
    'added' => site_selection_format_time($token['added_at']),
  ];
}

render_page('pages/admin/site-selection/list.html.twig', [
  'message' => $message,
  'available' => plural($available, 'key is', 'keys are'),
  'search' => $search,
  'sort' => $sort,
  'ascending' => $ascending,
  'clear_search_query' => http_build_query(array_diff_key($list_params, ['search' => ''])),
  'action_query' => http_build_query($list_params + ($page > 1 ? ['page' => $page] : [])),
  'total' => $total,
  'first' => ($page - 1) * PER_PAGE + 1,
  'columns' => $columns,
  'rows' => $rows,
  'pages' => pagination_links($page, $total_pages, $list_params),
]);
