<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

check_permission('manage-roles');

function is_admin_role($role_id) {
  return (db_get_role($role_id)['name'] ?? null) === ADMIN_ROLE_NAME;
}

// What the role form, which htmx swaps in place, shows for a role
function role_form_context($role_id) {
  return [
    'role_id' => $role_id,
    'roles' => db_get_roles(),
    'is_admin_role' => is_admin_role($role_id),
    'admin_role_name' => ADMIN_ROLE_NAME,
    'permissions' => db_get_role_permissions($role_id),
  ];
}

if (array_key_exists('HTTP_HX_REQUEST', $_SERVER)) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (array_key_exists('action', $_POST) && $_POST['action'] === 'delete') {
      $role_id = $_POST['role_id'];
      db_delete_role($role_id);
    } else if (array_key_exists('name', $_POST)) {
      $name = $_POST['name'];
      $role_id = db_create_role($name);
    } else if (!is_admin_role($_POST['role_id'])) {
      $role_id = $_POST['role_id'];
      $permissions = db_get_role_permissions($role_id);
      foreach ($permissions as $permission) {
        $has_permission = array_key_exists('permission_' . $permission['name'] . '_' . $permission['permission_id'], $_POST);
        db_set_role_permission($role_id, $permission['permission_id'], $has_permission);
      }
    } else {
      $role_id = $_POST['role_id'];
    }
  } else {
    $role_id = $_GET['role_id'];
  }
  echo twig()->render('partials/admin/role-form.html.twig', role_form_context($role_id));
  exit;
}

render_page('pages/admin/roles.html.twig', role_form_context($_GET['role_id'] ?? 1));
