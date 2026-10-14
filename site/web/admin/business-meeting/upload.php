<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/business_meeting.php');

check_permission('manage-business-meeting');

$error = null;
$counts = null;
$problems = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $file = $_FILES['csv'] ?? null;
  if ($file === null || is_array($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
    $error = 'Choose a CSV file to upload.';
  } else if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
    $error = 'The file is too big to upload.';
  } else if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
    $error = 'The file could not be uploaded. Please try again.';
  } else {
    [$links, $problems] = business_meeting_links_from_csv(file_get_contents($file['tmp_name']));
    $counts = db_set_business_meeting_links($links);
  }
}

render_header(
  'Upload links',
  'Upload business meeting join links.',
  [
    'Home' => '/',
    'Manage business meeting' => '/admin/business-meeting/list',
    'Upload links'
  ]
);
?>
  <article>
    <h2>Upload links</h2>
<?php
if ($error !== null) {
?>
    <p class="error"><img src="/resources/error.svg" alt="error" aria-label="error"><?php echo htmlspecialchars($error); ?></p>
<?php
}
if ($counts !== null) {
?>
    <p class="info">Added <?php echo $counts['added']; ?> and updated <?php echo $counts['updated']; ?> links. <a href="/admin/business-meeting/list">Back to the list</a>.</p>
<?php
}
if ($problems) {
?>
    <div class="error">
      <img src="/resources/error.svg" alt="error" aria-label="error">
      <p>Check these rows:</p>
      <ul>
<?php
  foreach ($problems as $problem) {
?>
        <li><?php echo htmlspecialchars($problem); ?></li>
<?php
  }
?>
      </ul>
    </div>
<?php
}
?>
    <p>Upload a CSV file of join links exported from <?php echo htmlspecialchars(BUSINESS_MEETING['platform_name']); ?>. The first row is a header naming the columns:</p>
    <ul>
      <li><strong>badge_no</strong>: the member's badge number.</li>
      <li><strong>join_link</strong>: their personal https:// join link.</li>
    </ul>
    <p>Other columns are ignored. If the header doesn't name these columns, the first column is used as the badge number and the second as the join link.</p>
    <p>A badge number that already has a link gets the new one, and if a badge number is in the file more than once, the last row wins. Rows with a missing badge number or link are skipped and listed. Links for badge numbers not in the file are left alone.</p>
    <form method="POST" action="/admin/business-meeting/upload" enctype="multipart/form-data" class="vertical">
      <?php echo csrf_field(); ?>
      <p><label>CSV file: <input type="file" name="csv" accept=".csv,text/csv" required></label></p>
      <input type="submit" value="Upload">
    </form>
  </article>
<?php
render_footer();
?>
