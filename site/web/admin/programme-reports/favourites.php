<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/conclar.php');
require_once(getenv('CONFIG_LIB_DIR') . '/csv.php');

check_permission('see-programme-reports');

$app_id = $_GET['app_id'] ?? (CONCLAR_VALID_APP_IDS[0] ?? '');
if (!is_string($app_id) || ($app_id !== '' && !conclar_validate_app_id($app_id))) {
  render_404();
}

$counts = $app_id === '' ? [] : conclar_get_favourite_counts($app_id);
$titles = $counts ? conclar_fetch_item_titles() : [];

if ($counts && ($_GET['download'] ?? '') === 'csv') {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="programme-favourites-' . date('Y-m-d') . '.csv"');

  $output = fopen('php://output', 'w');
  fputcsv($output, ['Item ID', 'Title', 'Favourites', 'Online', 'In person']);
  foreach ($counts as $row) {
    fputcsv($output, [
      csv_safe_cell($row['item_id']),
      csv_safe_cell($titles[$row['item_id']] ?? ''),
      $row['total'],
      $row['online'],
      $row['in_person'],
    ]);
  }
  fclose($output);
  exit;
}

render_header(
  'Programme item favourites',
  'How many members starred each programme item.',
  ['Home' => '/', 'Programme reports' => '/admin/programme-reports', 'Programme item favourites']
);
?>
<article>
  <h2>Programme item favourites</h2>
  <?php if ($app_id === ''): ?>
    <p>No ConClár app ids are configured, so there are no favourites to report.</p>
  <?php elseif (!$counts): ?>
    <p>No members have starred any items yet.</p>
  <?php else: ?>
    <p><?php echo conclar_count_members_with_favourites($app_id); ?> members have starred items, across <?php echo count($counts); ?> items. Members with neither the online nor the in-person role are only counted in the total.</p>
    <p><a href="/admin/programme-reports/favourites?app_id=<?php echo urlencode($app_id); ?>&amp;download=csv" class="button">Download CSV</a></p>
    <table>
      <thead>
        <tr><th>Item</th><th>Favourites</th><th>Online</th><th>In person</th></tr>
      </thead>
      <tbody>
        <?php foreach ($counts as $row): ?>
          <tr>
            <td><?php echo htmlspecialchars($titles[$row['item_id']] ?? $row['item_id']); ?></td>
            <td><?php echo (int) $row['total']; ?></td>
            <td><?php echo (int) $row['online']; ?></td>
            <td><?php echo (int) $row['in_person']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</article>
<?php
render_footer();
?>
