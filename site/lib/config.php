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
define('DISCORD_GUILD_ID', '1462809034812817635');
define('DISCORD_INVITE_CHANNEL_ID', '1463834467369091216');
define('DISCORD_API_CHANNEL_ID', '1481307681846333666');
define("SUPPORT_EMAIL", "info@eastercon2026.org");
define("REGISTRATION_EMAIL", "registration@eastercon2026.org");
define("TECH_EMAIL", "tech@eastercon2026.org");
define("PROGRAMME_EMAIL", "programme@eastercon2026.org");
define('TIMEZONE', 'Europe/London');
define('ALLOW_ANONYMOUS_ACCESS', true);
define('CONCLAR_VALID_APP_IDS', ['e26']);
define('CONCLAR_MAX_ITEMS', 300);

define('DEFAULT_ROLES_BY_BADGE', [
  'artist' => [
    '0785',
    '0744',
    '0345',
    '0164',
    '0425',
    '0063',
    '0300',
    '0298',
    '0266',
    '0189',
    '0056',
    '0246',
    '0004',
    '0456',
    '0797',
    '0396',
    '0128',
    '0364',
    '0707',
    '0292',
    '0523',
    '0170',
    '0603',
    '0710',
    '0331',
    '0437',
    '0553',
    '0121',
    '0380',
    '0036',
    '0304',
    '0340',
    '0421',
    '0358',
    '0499',
    '0578',
    '0233',
    '0423',
    '0289',
    '0589',
    '0386',
    '0524',
    '0178',
    '0683',
    '0398',
    '0079',
    '0012',
    '0628',
  ],
  'dealer' => [
    '0425',
    '0737',
    '0651',
    '0702',
    '0166',
    '0629',
    '0298',
    '0458',
    '0618',
    '0617',
    '0167',
    '0116',
    '0180',
    '0565',
    '0796',
    '0836',
    '0495',
    '0496',
    '0680',
    '0505',
    '0506',
    '0292',
    '0296',
    '0493',
    '0452',
    '0288',
    '0295',
    '0137',
    '0610',
    '0306',
    '0550',
    '0652',
    '0459',
    '0355',
    '0286',
    '0742',
    '0575',
    '0591'
  ],
  'programme moderator' => [
    '0056',
    '0248',
    '0665',
    '0737',
    '0298',
    '0131',
    '0036',
    '0014',
    '0190',
    '0010',
    '0529',
    '0341',
    '0354',
    '0004',
    '0102',
    '0028',
    '0538',
    '0423',
    '0152',
    '0541',
    '0596',
    '0188',
    '0039',
    '0206',
    '0512',
    '0159',
    '0226',
    '0525',
    '0166',
    '0386',
    '0267',
    '0020',
    '0209',
    '0322',
    '0032',
    '0309',
    '0375',
    '0202',
    '0128',
    '0460',
    '0699',
    '0479',
    '0196',
    '0519',
    '0355',
    '0349',
    '0180',
    '0704',
    '0252',
    '0037',
    '0396',
    '0240',
    '0452',
    '0135',
    '0308',
    '0282',
    '0697',
    '0221',
    '0417',
    '0485',
    '0012',
    '0294',
    '0288',
    '0117',
    '0424',
    '0246',
    '0137',
    '0243',
    '0320',
    '0399',
    '0757',
    '0613',
    '0612',
    '0026',
    '0030',
    '0305',
    '0249',
    '0003',
    '0001',
    '0729',
    '0464',
    '0397',
  ],
  'programme participant' => [
    '0056',
    '0308',
    '0085',
    '0522',
    '0104',
    '0248',
    '0374',
    '0295',
    '0458',
    '0373',
    '0004',
    '0665',
    '0393',
    '0471',
    '0470',
    '0737',
    '0424',
    '0637',
    '0037',
    '0012',
    '0001',
    '0036',
    '0298',
    '0003',
    '0131',
    '0014',
    '0002',
    '0005',
    '0190',
    '0010',
    '0228',
    '0229',
    '0152',
    '0133',
    '0538',
    '0650',
    '0473',
    '0710',
    '0323',
    '0234',
    '0467',
    '0092',
    '0529',
    '0128',
    '0640',
    '0341',
    '0354',
    '0168',
    '0163',
    '0102',
    '0243',
    '0028',
    '0320',
    '0254',
    '0160',
    '0501',
    '0423',
    '0166',
    '0093',
    '0452',
    '0396',
    '0083',
    '0021',
    '0497',
    '0541',
    '0332',
    '0331',
    '0275',
    '0596',
    '0272',
    '0196',
    '0586',
    '0154',
    '0188',
    '0149',
    '0585',
    '0583',
    '0267',
    '0029',
    '0039',
    '0206',
    '0512',
    '0280',
    '0699',
    '0159',
    '0511',
    '0240',
    '0289',
    '0375',
    '0443',
    '0391',
    '0576',
    '0226',
    '0214',
    '0525',
    '0704',
    '0032',
    '0209',
    '0457',
    '0334',
    '0560',
    '0386',
    '0268',
    '0589',
    '0020',
    '0492',
    '0350',
    '0322',
    '0619',
    '0180',
    '0253',
    '0399',
    '0316',
    '0294',
    '0683',
    '0309',
    '0519',
    '0137',
    '0675',
    '0566',
    '0575',
    '0288',
    '0397',
    '0202',
    '0286',
    '0459',
    '0613',
    '0460',
    '0221',
    '0523',
    '0479',
    '0778',
    '0304',
    '0554',
    '0696',
    '0446',
    '0217',
    '0355',
    '0572',
    '0349',
    '0706',
    '0592',
    '0464',
    '0139',
    '0417',
    '0116',
    '0299',
    '0439',
    '0252',
    '0712',
    '0713',
    '0612',
    '0765',
    '0282',
    '0057',
    '0606',
    '0135',
    '0170',
    '0169',
    '0736',
    '0697',
    '0726',
    '0461',
    '0303',
    '0493',
    '0329',
    '0333',
    '0485',
    '0096',
    '0141',
    '0117',
    '0719',
    '0015',
    '0246',
    '0691',
    '0660',
    '0757',
    '0026',
    '0030',
    '0305',
    '0022',
    '0249',
    '0178',
    '0189',
    '0097',
    '0729',
    '0011',
    '0013',
  ],
  'discord mod' => [
    '0033',
    '0056',
    '0282',
    '0138',
  ]
]);

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

if (isset($config_file['conclar'])) {
  define('CONCLAR_ALLOWED_ORIGINS', $config_file['conclar']['allowed_origins'] ?? []);
  define('COOKIE_DOMAIN', $config_file['conclar']['cookie_domain'] ?? '');
} else {
  define('CONCLAR_ALLOWED_ORIGINS', []);
  define('COOKIE_DOMAIN', '');
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

set_exception_handler('exception_handler');

?>