<?php

require 'vendor/autoload.php';

$is_https = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);
define('ROOT_URL', ($is_https ? 'https://' : 'http://') . (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']));
define('CON_NAME', 'Iridescence - The 2026 Eastercon');
define('CON_WEBSITE', 'https://eastercon2026.org');
define('CON_SHORT_NAME', 'Iridescence');
define('CON_START_DATE', '2026-04-03');
define('DISCORD_GUILD_ID', '1229093507835101204');
define('DISCORD_INVITE_CHANNEL_ID', '1229126688277926008');
define('DISCORD_API_CHANNEL_ID', '1256912513572929596');
define("SUPPORT_EMAIL", "info@eastercon2026.org");
define("REGISTRATION_EMAIL", "registration@eastercon2026.org");
define("TECH_EMAIL", "tech@eastercon2026.org");
define("PROGRAMME_EMAIL", "programme@eastercon2026.org");
define('TIMEZONE', 'Europe/London');
define('ALLOW_ANONYMOUS_ACCESS', true);

define('DISCORD_SERVICE_ACCOUNTS', [
]);

define('DB_HOST', getenv('CONFIG_DB_HOST'));
define('DB_NAME', getenv('CONFIG_DB_NAME'));
define('CONREG_DB_NAME', getenv('CONFIG_CONREG_DB_NAME'));
define('DB_USER', getenv('CONFIG_DB_USER'));
define('DB_PASSWORD', trim(file_get_contents(getenv('CONFIG_DB_PASSWORD_FILE')), "\r\n"));

$config_file = parse_ini_file(getenv('CONFIG_SECRETS_FILE'), true);
define('RCE_API_KEY', $config_file['rce']['api_key']);
define('RCE_TICKET_ID', $config_file['rce']['ticket_id']);
define('DISCORD_BOT_TOKEN', $config_file['discord']['bot_token']);
define('DISCORD_CLIENT_ID', $config_file['discord']['client_id']);
define('DISCORD_CLIENT_SECRET', $config_file['discord']['client_secret']);
define('API_KEYS', $config_file['api_keys']);

if (isset($config_file['oauth'])) {
  define('OAUTH_CLIENT_ID', $config_file['oauth']['client_id']);
  define('OAUTH_CLIENT_SECRET', $config_file['oauth']['client_secret']);
  define('OAUTH_SERVER_ENDPOINT', $config_file['oauth']['server_endpoint']);
  if (!empty($config_file['oauth']['allowlist'])) {
    define('OAUTH_ALLOWLIST', array_map('trim', $config_file['oauth']['allowlist']));
  }
}

if (!empty($config_file['conreg']['allowlist'])) {
    define('CONREG_ALLOWLIST', array_map('trim', $config_file['conreg']['allowlist']));
    define('CONREG_HARDCODED_MEMBERS', array_map(function($item) {
        $parts = array_map('trim', explode(',', $item));
        return [
            'badge_no' => (int) $parts[0],
            'email' => $parts[1],
            'name' => $parts[2],
            'member_type' => $parts[3],
        ];
    }, $config_file['conreg']['hardcoded_members']));
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