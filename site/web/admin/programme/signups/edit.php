<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/signups.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

$errors = [];
$id = null;
$values = array_fill_keys(SIGNUP_FIELDS, '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;
  $id = ($_POST['id'] ?? '') !== '' ? (int) $_POST['id'] : null;

  if ($action === 'delete') {
    try {
      db_delete_signup($id);
    } catch (Exception $e) {
      header('Location: /admin/programme/signups/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
    header('Location: /admin/programme/signups/list');
    exit;
  }

  foreach (SIGNUP_FIELDS as $field) {
    $values[$field] = (string) ($_POST[$field] ?? '');
  }
  [$signup, $errors] = signup_from_input($values);

  if (!$errors) {
    try {
      if ($action === 'edit') {
        db_edit_signup($id, $signup);
      } else {
        db_add_signup($signup);
      }
    } catch (Exception $e) {
      header('Location: /admin/programme/signups/list?error=' . urlencode($e->getMessage()));
      throw $e;
    }
    header('Location: /admin/programme/signups/list');
    exit;
  }
} else if (isset($_GET['id'])) {
  $id = (int) $_GET['id'];
  $signup = db_get_signup($id);
  if ($signup === null) {
    header('Location: /admin/programme/signups/list?error=' . urlencode('No such sign-up'));
    exit;
  }
  foreach (SIGNUP_FIELDS as $field) {
    $values[$field] = in_array($field, SIGNUP_TIME_FIELDS, true) ? signup_format_time($signup[$field], 'Y-m-d\TH:i') : (string) $signup[$field];
  }
}

$editing = $id !== null;
$page_title = $editing ? 'Edit sign-up' : 'Add sign-up';

render_header(
  'Add/edit a sign-up',
  'Manage sign-ups.',
  [
    'Home' => '/',
    'Manage programme' => '/admin/programme/list',
    'Manage sign-ups' => '/admin/programme/signups/list',
    $page_title
  ]
);

function signup_time_value($value) {
  // Redisplaying a rejected submission: datetime-local needs the 'T'
  return htmlspecialchars(str_replace(' ', 'T', trim($value)));
}
?>
  <article>
    <h2><?php echo htmlspecialchars($page_title); ?></h2>
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
    <form action="/admin/programme/signups/edit" method="POST" class="vertical">
      <?php echo csrf_field(); ?>
      <fieldset>
        <legend>Sign-up details</legend>
<?php
if ($editing) {
?>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) $id); ?>">
        <input type="hidden" name="action" value="edit">
<?php
} else {
?>
        <input type="hidden" name="action" value="add">
<?php
}
?>
        <p><label>Title: <input name="title" maxlength="256" required value="<?php echo htmlspecialchars($values['title']); ?>"></label></p>
        <p><label>Category (sign-ups are grouped by this, for example "Workshop"): <input name="category" maxlength="100" required value="<?php echo htmlspecialchars($values['category']); ?>"></label></p>
        <p><label>Sign-up URL: <input type="url" name="url" maxlength="2048" required value="<?php echo htmlspecialchars($values['url']); ?>"></label></p>
        <p>Use <code>{name}</code>, <code>{email}</code> and <code>{badge_no}</code> anywhere in the URL to fill in the member's details. They are removed for members who are not logged in.</p>
        <p><label>More information URL: <input type="url" name="guide_url" maxlength="2048" value="<?php echo htmlspecialchars($values['guide_url']); ?>"></label></p>
        <p><label>Notes: <textarea name="notes" rows="3"><?php echo htmlspecialchars($values['notes']); ?></textarea></label></p>
        <p>Times are in <?php echo htmlspecialchars(TIMEZONE); ?>.</p>
        <p><label>Session start (used to group by day and time): <input type="datetime-local" name="session_start" value="<?php echo signup_time_value($values['session_start']); ?>"></label></p>
        <p><label>Sign-ups open (leave empty to open now): <input type="datetime-local" name="opens_at" value="<?php echo signup_time_value($values['opens_at']); ?>"></label></p>
        <p><label>Sign-ups close (leave empty to stay open): <input type="datetime-local" name="closes_at" value="<?php echo signup_time_value($values['closes_at']); ?>"></label></p>
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
    <form id="delete-form" action="/admin/programme/signups/edit" method="POST" hidden>
      <?php echo csrf_field(); ?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) $id); ?>">
    </form>
    <script>
      document.getElementById('delete').addEventListener('click', function() {
        if (confirm('Are you sure you want to delete ' + <?php echo json_encode($values['title'], JSON_HEX_TAG | JSON_HEX_AMP); ?> + '?')) {
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
