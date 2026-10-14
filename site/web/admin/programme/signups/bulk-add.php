<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/signups.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

$csv = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $csv = (string) ($_POST['csv'] ?? '');
  [$signups, $errors] = signups_from_csv($csv);

  if (!$errors) {
    try {
      db_add_signups($signups);
    } catch (Exception $e) {
      header('Location: /admin/programme/signups/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
    header('Location: /admin/programme/signups/list');
    exit;
  }
}

render_header(
  'Bulk add sign-ups',
  'Bulk add sign-ups.',
  [
    'Home' => '/',
    'Manage programme' => '/admin/programme/list',
    'Manage sign-ups' => '/admin/programme/signups/list',
    'Bulk add sign-ups'
  ]
);
?>
  <article>
    <h2>Bulk add sign-ups</h2>
<?php
if ($errors) {
?>
    <div class="error">
      <img src="/resources/error.svg" alt="error" aria-label="error">
      <p>Nothing was imported. Fix these and try again:</p>
      <ul>
<?php
  foreach ($errors as $error) {
?>
        <li><?php echo htmlspecialchars($error); ?></li>
<?php
  }
?>
      </ul>
    </div>
<?php
}
?>
    <p>Paste CSV with a header row naming the columns. Columns can be in any order, and only <strong>title</strong>, <strong>category</strong> and <strong>url</strong> are required. Rows are numbered from the first row after the header.</p>
    <ul>
      <li><strong>title</strong>: the item's name, shown as the heading.</li>
      <li><strong>category</strong>: sign-ups are grouped by this, for example "Workshop".</li>
      <li><strong>url</strong>: the sign-up form. <code>{name}</code>, <code>{email}</code> and <code>{badge_no}</code> anywhere in the URL are replaced with the member's details.</li>
      <li><strong>guide_url</strong>: a link to more information.</li>
      <li><strong>notes</strong>: shown under the title.</li>
      <li><strong>session_start</strong>: when the item happens, used to group by day and time.</li>
      <li><strong>opens_at</strong>, <strong>closes_at</strong>: when sign-ups open and close.</li>
    </ul>
    <p>Times are <em>yyyy-mm-dd hh:mm</em> in 24 hour <?php echo htmlspecialchars(TIMEZONE); ?> time.</p>
    <form method="POST" action="/admin/programme/signups/bulk-add" class="vertical">
      <p><label>CSV: <textarea name="csv" rows="15" required placeholder="title,category,url,session_start,opens_at,closes_at"><?php echo htmlspecialchars($csv); ?></textarea></label></p>
      <input type="submit" value="Import">
    </form>
  </article>
<?php
render_footer();
?>
