<?php
require_once('auth_functions.php');
require_once('db.php');

if (!is_logged_in()) {
  header('Location: /login');
  exit;
}

$_SESSION['username'] = get_current_user_name();

if (!isset($_COOKIE['session'])) {
  $permissions = [];
} else {
  $permissions = db_get_permissions_by_session($_COOKIE['session']);

  if (isset($_GET['role']) && in_array('manage-roles', $permissions)) {
    $default_role_id = db_get_role_id('default');
    $default_role_permissions = array_column(array_filter(db_get_role_permissions($default_role_id), function($role_permission) {
      return $role_permission['has_permission'];
    }), 'name');

    // Filter our current permissions to only those that are also allowed by the selected role
    $role_id = db_get_role_id($_GET['role']);
    $role_permissions = db_get_role_permissions($role_id);
    foreach ($role_permissions as $role_permission) {
      if (!$role_permission['has_permission'] && !in_array($role_permission['name'], $default_role_permissions)) {
        if (($key = array_search($role_permission['name'], $permissions)) !== false) {
          unset($permissions[$key]);
        }
      }
    }
  }
}

$_SESSION['permissions'] = $permissions;

?>