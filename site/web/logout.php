<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');

logout();

$redirect = $_GET['redirect'] ?? '/login';
if (!is_valid_redirect($redirect)) {
    $redirect = '/login';
}
header('Location: ' . $redirect);
?>