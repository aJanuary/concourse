<?php

$GLOBALS['app-data'] ??= [];

$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
  || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
  || ($_SERVER['SERVER_PORT'] ?? '') == 443;
define('IS_HTTPS', $is_https);
define('ROOT_URL', ($is_https ? 'https://' : 'http://') . ($_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST']));
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

define('DB_DRIVER', getenv('CONFIG_DB_DRIVER') ?: 'mysql');
if (!in_array(DB_DRIVER, ['mysql', 'pgsql'], true)) {
  throw new RuntimeException("Unsupported CONFIG_DB_DRIVER '" . DB_DRIVER . "'");
}
define('DB_HOST', getenv('CONFIG_DB_HOST'));
define('DB_PORT', getenv('CONFIG_DB_PORT') ?: '');
define('DB_NAME', getenv('CONFIG_DB_NAME'));
define('DB_USER', getenv('CONFIG_DB_USER'));
define('DB_PASSWORD', trim(file_get_contents(getenv('CONFIG_DB_PASSWORD_FILE')), "\r\n"));

$config_file = parse_ini_file(getenv('CONFIG_SECRETS_FILE'), true);

// Every section is optional so that an install only needs the sections for
// the features it uses.
function config_get($section, $key, $default = '') {
  global $config_file;
  return $config_file[$section][$key] ?? $default;
}

// Call before using an integration so an unconfigured one fails with a clear
// message rather than a confusing error from the remote service.
function require_config($section, ...$keys) {
  foreach ($keys as $key) {
    if (config_get($section, $key) === '') {
      throw new RuntimeException("The secrets file must set '$key' in the [$section] section to use this feature");
    }
  }
}

define('RCE_API_KEY', config_get('rce', 'api_key'));
define('RCE_TICKET_ID', config_get('rce', 'ticket_id'));
define('DISCORD_BOT_TOKEN', config_get('discord', 'bot_token'));
define('DISCORD_CLIENT_ID', config_get('discord', 'client_id'));
define('DISCORD_CLIENT_SECRET', config_get('discord', 'client_secret'));
define('API_KEYS', $config_file['api_keys'] ?? []);

define('ALLOWLIST', array_map('trim', config_get('auth', 'allowlist', [])));
define('AUTH_PROVIDERS', array_map('trim', config_get('auth', 'providers', [])));

// The default name keeps existing sessions valid
define('SESSION_COOKIE_NAME', config_get('cookie', 'cookie_name') ?: 'session');
define('SESSION_COOKIE_DOMAIN', config_get('cookie', 'cookie_domain'));

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
