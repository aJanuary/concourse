<?php
require_once('auth_functions.php');

if (!is_logged_in()) {
  header('Location: /login/');
  exit;
}
?>