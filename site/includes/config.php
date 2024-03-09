<?php

require_once('secrets.php');

define('ROOT_URL', 'https://members' . (STAGING !== null ? '-staging' : '') . '.eastercon2024.co.uk');
define('CON_NAME', 'Levitation 2024');
define('DB_HOST', 'localhost');
define('DB_NAME', 'u943682649_members' . (STAGING !== null ? '_stagin' : ''));
define('DB_USER', 'u943682649_members' . (STAGING !== null ? '_stagin' : ''));
define('HOPIN_TICKET_ID', 'KaO15ej7TA7lroiNMIhiruo4a');
define('DISCORD_CLIENT_ID', '1123127114447736883');
define('DISCORD_CHANNEL_ID', '970988942704922645');
define("SMTP_ADDRESS", "smtp.hostinger.com");
define("SMTP_PORT","465");
define("SMTP_USER", "info@eastercon2024.co.uk");
define("EMAIL", "info@eastercon2024.co.uk");

function log_exception(Throwable $exception) {
  $err_code = bin2hex(random_bytes(8));
  $indented_trace = preg_replace('/^/m', '  ', $exception->getTraceAsString());
  error_log("Uncaught exception [$err_code]: " . $exception->getMessage() . "\n" . $indented_trace . "\n");
  return $err_code;
};

function exception_handler(Throwable $exception) {
  $err_code = log_exception($exception);
  echo "Sorry, something has gone wrong. Please try again. If the problem persists, contact <a href=\"mailto:" . EMAIL . "?subject=Problem with member portal&body=Code: $err_code%0D%0A%0D%0AProblem:%0D%0A\">" . EMAIL . "</a> quoting the code \"$err_code\"\n";
  exit();
}

set_exception_handler('exception_handler');

?>