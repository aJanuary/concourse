<?php

define('ROOT_URL', 'https://' . $_SERVER['HTTP_X_FORWARDED_HOST'] ?? _SERVER['HTTP_HOST']);
define('CON_NAME', 'Glasgow 2024, a Worldcon for Our Futures');
define('CON_SHORT_NAME', 'Glasgow 2024');
define('DISCORD_GUILD_ID', '1229093507835101204');
define('DISCORD_INVITE_CHANNEL_ID', '1229126688277926008');
define('DISCORD_API_CHANNEL_ID', '1248917066807775232');
define("SUPPORT_EMAIL", "online-support@glasgow2024.org");
define('TIMEZONE', 'Europe/London');

define('DB_HOST', getenv('CONFIG_DB_HOST'));
define('DB_NAME', getenv('CONFIG_DB_NAME'));
define('DB_USER', getenv('CONFIG_DB_USER'));
define('DB_PASSWORD', trim(file_get_contents(getenv('CONFIG_DB_PASSWORD_FILE')), "\r\n"));

$config_file = parse_ini_file(getenv('CONFIG_SECRETS_FILE'), true);
define('RCE_API_KEY', $config_file['rce']['api_key']);
define('RCE_TICKET_ID', $config_file['rce']['ticket_id']);
define('DISCORD_BOT_TOKEN', $config_file['discord']['bot_token']);
define('DISCORD_CLIENT_ID', $config_file['discord']['client_id']);
define('DISCORD_CLIENT_SECRET', $config_file['discord']['client_secret']);
define('API_KEYS', $config_file['api_keys']);

define('CLYDE_CLIENT_ID', $config_file['clyde']['client_id']);
define('CLYDE_CLIENT_SECRET', $config_file['clyde']['client_secret']);
define('CLYDE_SERVER_ENDPOINT', $config_file['clyde']['server_endpoint']);
if (!empty($config_file['clyde']['allowlist'])) {
  define('CLYDE_ALLOWLIST', array_map('trim', $config_file['clyde']['allowlist']));
}

function log_exception(Throwable $exception) {
  $err_code = bin2hex(random_bytes(8));
  $indented_trace = preg_replace('/^/m', '  ', $exception->getTraceAsString());
  error_log("Uncaught exception [$err_code]: " . $exception->getMessage() . "\n" . $indented_trace . "\n");
  return $err_code;
};

function exception_handler(Throwable $exception) {
  $err_code = log_exception($exception);
  echo "Sorry, something has gone wrong. Please try again. If the problem persists, contact <a href=\"mailto:" . SUPPORT_EMAIL . "?subject=Problem with member portal&body=Code: $err_code%0D%0A%0D%0AProblem:%0D%0A\">" . SUPPORT_EMAIL . "</a> quoting the code \"$err_code\"\n";
  exit();
}

session_start();

set_exception_handler('exception_handler');

?>