<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
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
$heading = $editing ? 'Edit sign-up' : 'Add sign-up';

// Redisplaying a rejected submission: datetime-local needs the 'T'
foreach (SIGNUP_TIME_FIELDS as $field) {
  $values[$field] = str_replace(' ', 'T', trim($values[$field]));
}

render_page('pages/admin/programme/signups/edit.html.twig', [
  'heading' => $heading,
  'errors' => $errors,
  'editing' => $editing,
  'id' => $id,
  'values' => $values,
  'timezone' => TIMEZONE,
]);
