<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/business_meeting.php');

check_permission('manage-business-meeting');

function redirect_to_list($done, $badge_no) {
  header('Location: /admin/business-meeting/list?' . http_build_query(['done' => $done, 'badge_no' => $badge_no]));
  exit;
}

$errors = [];
$editing = false;
$badge_no = '';
$join_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;
  $editing = $action === 'edit';
  $badge_no = trim((string) ($_POST['badge_no'] ?? ''));
  $join_link = trim((string) ($_POST['join_link'] ?? ''));

  if ($action === 'delete') {
    db_delete_business_meeting_link($badge_no);
    redirect_to_list('deleted', $badge_no);
  }

  $errors = array_filter([business_meeting_badge_error($badge_no), business_meeting_link_error($join_link)]);
  if (!$errors && !$editing && db_get_business_meeting_link($badge_no) !== null) {
    $errors[] = "Badge $badge_no already has a link. Edit it from the list instead.";
  }
  if (!$errors) {
    db_set_business_meeting_link($badge_no, $join_link);
    redirect_to_list('saved', $badge_no);
  }
} else if (isset($_GET['badge_no'])) {
  $badge_no = (string) $_GET['badge_no'];
  $join_link = db_get_business_meeting_link($badge_no);
  if ($join_link === null) {
    render_404();
  }
  $editing = true;
}

render_page('pages/admin/business-meeting/edit.html.twig', [
  'heading' => $editing ? "Edit the link for badge $badge_no" : 'Add a link',
  'errors' => $errors,
  'editing' => $editing,
  'badge_no' => $badge_no,
  'join_link' => $join_link,
  'platform' => BUSINESS_MEETING['platform_name'],
  'badge_max_length' => BUSINESS_MEETING_BADGE_MAX_LENGTH,
  'link_max_length' => BUSINESS_MEETING_LINK_MAX_LENGTH,
]);
