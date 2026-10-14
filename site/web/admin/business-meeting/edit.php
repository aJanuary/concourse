<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
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

$page_title = $editing ? 'Edit link' : 'Add link';

render_header(
  $page_title,
  'Manage business meeting join links.',
  [
    'Home' => '/',
    'Manage business meeting' => '/admin/business-meeting/list',
    $page_title
  ]
);
?>
  <article>
    <h2><?php echo htmlspecialchars($editing ? "Edit the link for badge $badge_no" : 'Add a link'); ?></h2>
<?php
if ($errors) {
?>
    <div class="error">
      <img src="/resources/error.svg" alt="error" aria-label="error">
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
    <form action="/admin/business-meeting/edit" method="POST" class="vertical">
      <?php echo csrf_field(); ?>
      <fieldset>
        <legend>Join link</legend>
<?php
if ($editing) {
?>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="badge_no" value="<?php echo htmlspecialchars($badge_no); ?>">
<?php
} else {
?>
        <input type="hidden" name="action" value="add">
        <p><label>Badge number: <input name="badge_no" maxlength="<?php echo BUSINESS_MEETING_BADGE_MAX_LENGTH; ?>" required value="<?php echo htmlspecialchars($badge_no); ?>"></label></p>
<?php
}
?>
        <p><label><?php echo htmlspecialchars(BUSINESS_MEETING['platform_name']); ?> join link: <input type="url" name="join_link" maxlength="<?php echo BUSINESS_MEETING_LINK_MAX_LENGTH; ?>" required pattern="https://.*" value="<?php echo htmlspecialchars($join_link); ?>"></label></p>
      </fieldset>
      <input type="submit" value="Save">
<?php
if ($editing) {
?>
      <input id="delete" type="button" value="Delete">
<?php
}
?>
    </form>
<?php
if ($editing) {
?>
    <form id="delete-form" action="/admin/business-meeting/edit" method="POST" hidden>
      <?php echo csrf_field(); ?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="badge_no" value="<?php echo htmlspecialchars($badge_no); ?>">
    </form>
    <script>
      document.getElementById('delete').addEventListener('click', function() {
        if (confirm('Are you sure you want to delete the link for badge ' + <?php echo json_encode($badge_no, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?> + '?')) {
          document.getElementById('delete-form').submit();
        }
      });
    </script>
<?php
}
?>
  </article>
<?php
render_footer();
?>
