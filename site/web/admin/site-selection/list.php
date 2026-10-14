<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
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

function sort_link($column, $title) {
  global $sort, $ascending, $search;
  $ascending_next = !($sort === $column && $ascending);
  $params = array_filter(['search' => $search, 'sort' => $column, 'dir' => $ascending_next ? 'asc' : 'desc']);
  $indicator = $sort === $column ? ($ascending ? ' ▲' : ' ▼') : '';
  $aria_sort = $sort === $column ? ($ascending ? 'ascending' : 'descending') : null;
  return '<th' . ($aria_sort ? ' aria-sort="' . $aria_sort . '"' : '') . '><a href="?' . htmlspecialchars(http_build_query($params)) . '">' . htmlspecialchars($title) . $indicator . '</a></th>';
}

function token_status($token) {
  if ($token['badge_no'] === null) {
    return 'Available';
  }
  $who = $token['badge_no'] . ($token['name'] !== null ? ' (' . $token['name'] . ')' : '');
  return ($token['spoilt_at'] !== null ? 'Spoilt, was ' : 'Assigned to ') . $who;
}

render_header(
  'Manage site selection',
  'Manage site selection voter keys.',
  ['Home' => '/', 'Manage site selection']
);
?>
<article class="full-width">
  <h2>Manage site selection</h2>
<?php
if ($message !== null) {
?>
  <p class="info" role="status"><?php echo htmlspecialchars($message); ?></p>
<?php
}
?>
  <p>These are the voter keys from the election platform. Members who pay online are each assigned one that is available. <?php echo htmlspecialchars(plural($available, 'key is', 'keys are')); ?> available.</p>
  <ul>
    <li><a href="/admin/site-selection/add">Add keys...</a></li>
    <li><a href="/admin/site-selection/spoilt">Spoilt keys</a>, to void on the election platform</li>
    <li><a href="/admin/site-selection/export">Export member details</a> for the winning bid</li>
<?php
if (current_user_has_permission('sell-tokens')) {
?>
    <li><a href="/admin/site-selection/desk">Site selection desk</a></li>
<?php
}
?>
  </ul>

  <form method="GET" class="inline-actions">
    <label for="search">Search labels and keys</label>
    <input type="search" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>">
<?php
if ($sort !== 'added_at' || $ascending) {
?>
    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
    <input type="hidden" name="dir" value="<?php echo $ascending ? 'asc' : 'desc'; ?>">
<?php
}
?>
    <input type="submit" value="Search">
<?php
if ($search !== '') {
?>
    <a href="?<?php echo htmlspecialchars(http_build_query(array_diff_key($list_params, ['search' => '']))); ?>">Clear</a>
<?php
}
?>
  </form>
<?php
if ($total === 0) {
?>
  <p><?php echo $search !== '' ? 'No keys match your search.' : 'There are no keys yet.'; ?></p>
<?php
} else {
  $first = ($page - 1) * PER_PAGE + 1;
?>
  <p>Showing <?php echo $first; ?>&ndash;<?php echo $first + count($tokens) - 1; ?> of <?php echo $total; ?> keys.</p>
  <form method="POST" action="?<?php echo htmlspecialchars(http_build_query($list_params + ($page > 1 ? ['page' => $page] : []))); ?>">
    <?php echo csrf_field(); ?>
    <div class="inline-actions">
      <button type="submit" class="button" name="bulk_action" value="unassign"
        data-confirm-title="Unassign the selected keys?"
        data-confirm-message="Each goes back to the pool to be given to someone else, even if it was spoilt. A member who had one can no longer vote with it."
        data-confirm-button="Unassign keys">Unassign selected</button>
      <button type="submit" class="button" name="bulk_action" value="delete"
        data-confirm-title="Delete the selected keys?"
        data-confirm-message="This can't be undone. Keys that are assigned aren't deleted."
        data-confirm-button="Delete keys">Delete selected</button>
    </div>
    <table>
      <thead>
        <tr>
          <th><input type="checkbox" id="select-all" aria-label="Select all keys on this page"></th>
          <?php echo sort_link('label', 'Label'); ?>
          <?php echo sort_link('token', 'Key'); ?>
          <?php echo sort_link('badge_no', 'Status'); ?>
          <?php echo sort_link('added_by', 'Added by'); ?>
          <?php echo sort_link('added_at', 'Added'); ?>
        </tr>
      </thead>
      <tbody>
<?php
  foreach ($tokens as $token) {
?>
        <tr>
          <td><input type="checkbox" name="ids[]" value="<?php echo $token['id']; ?>" aria-label="Select <?php echo htmlspecialchars($token['label'] ?? $token['token']); ?>"></td>
          <td><?php echo htmlspecialchars($token['label'] ?? ''); ?></td>
          <td><code><?php echo htmlspecialchars($token['token']); ?></code></td>
          <td><?php echo htmlspecialchars(token_status($token)); ?></td>
          <td><?php echo htmlspecialchars($token['added_by'] . ($token['added_by_name'] !== null ? ' (' . $token['added_by_name'] . ')' : '')); ?></td>
          <td><?php echo htmlspecialchars(site_selection_format_time($token['added_at'])); ?></td>
        </tr>
<?php
  }
?>
      </tbody>
    </table>
  </form>
<?php
  render_pagination($page, $total_pages, $list_params);
}
?>
</article>
<script src="<?php echo get_internal_url('/resources/confirm.js'); ?>" defer></script>
<script>
document.getElementById('select-all')?.addEventListener('change', function() {
  document.querySelectorAll('input[name="ids[]"]').forEach($box => $box.checked = this.checked);
});
</script>
<?php
render_footer();
?>
