<?php
// Loads the real config so PHPStan knows the constants it defines from the
// secrets file and the environment. Run inside the web container, which has both.
$_SERVER['HTTP_HOST'] ??= 'localhost';
require_once getenv('CONFIG_LIB_DIR') . '/config.php';
