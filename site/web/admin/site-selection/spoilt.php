<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');

check_permission('manage-tokens');

const PER_PAGE = 50;

$page = current_page_number();
$total = db_count_spoilt_site_selection_tokens();
$total_pages = max(1, (int) ceil($total / PER_PAGE));
$tokens = db_get_spoilt_site_selection_tokens(PER_PAGE, ($page - 1) * PER_PAGE);

render_header(
  'Spoilt keys',
  'Spoilt site selection voter keys.',
  ['Home' => '/', 'Manage site selection' => '/admin/site-selection/list', 'Spoilt keys']
);
?>
<article>
  <h2>Spoilt keys</h2>
  <p>A member's online key is spoilt when they move to a paper ballot or are given a new key. Void these keys on the election platform so they can't be used, newest first.</p>
<?php
if ($total === 0) {
?>
  <p>No keys have been spoilt.</p>
<?php
} else {
  $first = ($page - 1) * PER_PAGE + 1;
?>
  <p>Showing <?php echo $first; ?>&ndash;<?php echo $first + count($tokens) - 1; ?> of <?php echo $total; ?> keys.</p>
  <table>
    <thead>
      <tr><th>Label</th><th>Spoilt</th></tr>
    </thead>
    <tbody>
<?php
  foreach ($tokens as $token) {
?>
      <tr>
        <td><?php echo $token['label'] !== null ? htmlspecialchars($token['label']) : '<code>' . htmlspecialchars($token['token']) . '</code>'; ?></td>
        <td><?php echo htmlspecialchars(site_selection_format_time($token['spoilt_at'])); ?></td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>
<?php
  render_pagination($page, $total_pages);
}
?>
</article>
<?php
render_footer();
?>
