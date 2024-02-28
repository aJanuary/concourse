<?php
require_once('../../includes/config.php');
require_once('../../includes/session_auth.php');

logout();
header('Location: /login/');
?>