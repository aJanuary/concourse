<!doctype html>
<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

$message = "No database connection";
?>
<html>
  <body>
    <h1><?php echo $message; ?></hjson>
  </body>
</html>