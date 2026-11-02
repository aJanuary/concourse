<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
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

render_page('pages/admin/business-meeting/upload.html.twig', [
  'error' => $error,
  'counts' => $counts,
  'problems' => $problems,
  'platform' => BUSINESS_MEETING['platform_name'],
]);
