<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');

// Read before logout() deletes the session
$session_id = get_current_session_id();
$id_token = $session_id !== null && auth_get_provider('oidc') !== null ? db_get_oidc_id_token($session_id) : null;

logout();
if ($id_token !== null) {
  auth_get_provider('oidc')->sign_out($id_token);
}
header('Location: /login');
?>
