<?php

require 'vendor/autoload.php';

$is_https = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);
define('ROOT_URL', ($is_https ? 'https://' : 'http://') . (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']));
define('CON_NAME', 'Example Convention, a great tagline for a great demo');
define('CON_SHORT_NAME', 'ExampleCon');
define('CON_START_DATE', '2025-01-01');
define('DISCORD_GUILD_ID', '1229093507835101204');
define('DISCORD_INVITE_CHANNEL_ID', '1229126688277926008');
define('DISCORD_API_CHANNEL_ID', '1256912513572929596');
define("SUPPORT_EMAIL", "online-convention-help@example.com");
define("REGISTRATION_EMAIL", "registration@example.com");
define("TECH_EMAIL", "tech@example.com");
define("PROGRAMME_EMAIL", "programme@example.com");
define('TIMEZONE', 'Europe/London');
define('ALLOW_ANONYMOUS_ACCESS', true);

define('DISCORD_SERVICE_ACCOUNTS', [
  '1259068933491789854' => [
    'name' => 'Con Ops (SEC)',
    'roles' => ['Con-Ops'],
  ],
  '1259501220805083147' => [
    'name' => 'Con Ops (Crowne Plaza)',
    'roles' => ['Con-Ops'],
  ],
  '1259503664934948867' => [
    'name' => 'Team Info (Hall 4)',
    'roles' => ['Team-Info'],
  ],
  '1263552405820407960' => [
    'name' => 'Tech Ops (SEC)',
    'roles' => ['AV-Tech'],
  ],
  '1263553178549489737' => [
    'name' => 'Tech Ops (Crowne Plaza)',
    'roles' => ['AV-Tech'],
  ],
  '1264516078046810194' => [
    'name' => 'Volunteer Desk (Hall 4)',
    'roles' => ['Volunteer-Desk'],
  ],
  '1270307680208158751' => [
    'name' => 'Green Room (SEC)',
    'roles' => ['Green-Room'],
  ],
  '1270310702875279362' => [
    'name' => 'Green Room (Crowne Plaza)',
    'roles' => ['Green-Room'], 
  ],
  '1270311931340652564' => [
    'name' => 'Autographing (Hall 4)',
    'roles' => [],
  ],
  '1269310439616020673' => [
    'name' => 'Table Talks (Hall 4)',
    'roles' => []
  ],
  '1269738272284872829' => [
    'name' => 'Social Media (SEC)',
    'roles' => [],
  ],
  '1087801330929766560' => [
    'name' => 'Press Office (SEC)',
    'roles' => [],
  ],
  '1270065860769353810' => [
    'name' => 'Con Office (SEC)',
    'roles' => [],
  ],
  '1269312072043466848' => [
    'name' => 'Merchandise (SEC)',
    'roles' => [],
  ]
]);

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

define('OAUTH_CLIENT_ID', $config_file['oauth']['client_id']);
define('OAUTH_CLIENT_SECRET', $config_file['oauth']['client_secret']);
define('OAUTH_SERVER_ENDPOINT', $config_file['oauth']['server_endpoint']);
if (!empty($config_file['oauth']['allowlist'])) {
  define('OAUTH_ALLOWLIST', array_map('trim', $config_file['oauth']['allowlist']));
}
define('SMTP_HOST', $config_file['smtp']['host']);
define('SMTP_PORT', $config_file['smtp']['port']);
define('SMTP_USERNAME', $config_file['smtp']['username']);
define('SMTP_PASSWORD', $config_file['smtp']['password']);
define('SMTP_SECURE', $config_file['smtp']['secure']);
define('SIGNING_KEY', $config_file['crypto']['signing_key']);


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

set_exception_handler('exception_handler');

?>