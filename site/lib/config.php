<?php

$GLOBALS['app-data'] ??= [];

$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
  || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
  || ($_SERVER['SERVER_PORT'] ?? '') == 443;
define('IS_HTTPS', $is_https);
define('ROOT_URL', ($is_https ? 'https://' : 'http://') . ($_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST']));
define('CON_NAME', 'Example Convention, a great tagline for a great demo');
define('CON_WEBSITE', 'https://example.com');
define('REGISTRATION_URL', 'https://example.com/register');
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

// Roles given to a member when they log in, keyed by role name and listing the
// badge numbers that get it, e.g. ['artist' => ['0042', '0043']]. Roles are
// only ever added.
define('DEFAULT_ROLES_BY_BADGE', []);

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

// The business meeting page. Members with see-business-meeting see it, and
// those with a join link uploaded under Manage business meeting can join. See
// docs/integrations/business-meeting.md.
define('BUSINESS_MEETING', [
  // The meeting platform that gives each member their own join link
  'platform_name' => 'Lumi',
  'platform_info_url' => 'https://www.lumiglobal.com/',
  // The convention's page about the business meeting, and optional guidance
  // for attendees (null for none)
  'info_url' => 'https://example.com/business-meeting',
  'guide_url' => null,
  'help_email' => 'business-meeting@example.com',
  // Shown to members without a join link, after the usual reasons, for
  // example how long before a session a new member's link appears. Empty for none.
  'no_link_note' => '',
  // When each session starts, as 'yyyy-mm-dd hh:mm' in TIMEZONE
  'sessions' => ['2025-01-01 10:00'],
  // Shown under the dates, for example 'Each session lasts about three hours.'
  'duration' => '',
  // The Mark Protection Committee election, or null for none:
  // ['url' => 'https://example.com/mpc-election', 'opens' => '2025-01-01 12:00', 'closes' => '2025-01-02 12:00']
  'mpc_election' => null,
]);

// Site selection. Members with see-site-selection pay the site selection fee,
// an advance membership of the Worldcon being voted on, through Square and
// vote online on the election platform; the desk sells paper ballots. See
// docs/integrations/site-selection.md.
define('SITE_SELECTION', [
  // The Worldcon being voted on: its year and its number, e.g. '85th'
  'year' => '2027',
  'ordinal' => '85th',
  // The fee as shown to members. The Square catalogue item sets the charge.
  'fee' => '$50 USD',
  // Shown in a random order. A withdrawn bid is struck through.
  'bids' => [
    ['name' => 'Example City', 'url' => 'https://example.com/bid', 'withdrawn' => false],
  ],
  // The convention's page about how site selection works, and its paper ballot
  'info_url' => 'https://example.com/site-selection',
  'paper_ballot_url' => 'https://example.com/site-selection-ballot.pdf',
  // The election platform's page for voting with a voter key, with {token}
  // where the key goes
  'ballot_url' => 'https://example.com/vote/{token}',
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

// The key for encrypted secrets, from the CONFIG_ENCRYPTION_KEY environment
// variable, the file named by CONFIG_ENCRYPTION_KEY_FILE, or [encryption]
// config_encryption_key, in that order. Empty if none is set.
function config_encryption_key() {
  $key = trim((string) getenv('CONFIG_ENCRYPTION_KEY'));
  if ($key !== '') {
    return $key;
  }
  $key_file = (string) getenv('CONFIG_ENCRYPTION_KEY_FILE');
  if ($key_file !== '') {
    $contents = @file_get_contents($key_file);
    if ($contents === false) {
      throw new RuntimeException("Can't read the encryption key file '$key_file' named by CONFIG_ENCRYPTION_KEY_FILE");
    }
    $key = trim($contents);
    if ($key !== '') {
      return $key;
    }
  }
  return trim((string) config_get('encryption', 'config_encryption_key'));
}

const CONFIG_ENCRYPTED_PREFIX = 'enc:';

// Like config_get(), for keys and passwords. A value starting with enc: is
// decrypted with config_encryption_key(); other values are returned as they
// are. Decrypting only when asked means a problem with one secret only
// breaks the feature that uses it. See "Encrypted secrets" in
// docs/configuration.md.
function config_get_secret($section, $key, $default = '') {
  static $decrypted = [];

  $value = config_get($section, $key, $default);
  if (!is_string($value) || !str_starts_with($value, CONFIG_ENCRYPTED_PREFIX)) {
    return $value;
  }
  if (!isset($decrypted[$section][$key])) {
    require_once(__DIR__ . '/encryption.php');
    $encryption_key = config_encryption_key();
    if ($encryption_key === '') {
      throw new RuntimeException("'$key' in the [$section] section of the secrets file is encrypted, but no encryption key is set");
    }
    try {
      $decrypted[$section][$key] = decrypt_config_value(substr($value, strlen(CONFIG_ENCRYPTED_PREFIX)), $encryption_key);
    } catch (RuntimeException $e) {
      throw new RuntimeException("Can't decrypt '$key' in the [$section] section of the secrets file: " . $e->getMessage(), 0, $e);
    }
  }
  return $decrypted[$section][$key];
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

// The handler used for programme items that don't name one, and the handlers
// an item may name. See lib/prog_handlers.php.
define('DEFAULT_PROG_HANDLER', 'zoomevents');
define('AVAILABLE_PROG_HANDLERS', ['zoomevents', 'stream', 'discord']);
// The handler that opens replays
define('PROG_REPLAY_HANDLER', 'stream');
// Whether a member must be logged in to open a replay. Joining a live item
// always needs a login.
define('PROG_REPLAY_REQUIRES_LOGIN', true);
// Permission needed to join an item or open a replay. Empty means any
// logged in member.
define('PROG_JOIN_PERMISSION', 'see-rce');
// While true, joining an item shows a "you're early" page instead, for
// testing links before the convention starts
define('PROG_TEST_MODE', false);

define('ZOOM_EVENTS_CLIENT_ID', config_get('zoom_events', 'client_id'));
define('ZOOM_EVENTS_ACCOUNT_ID', config_get('zoom_events', 'account_id'));
define('ZOOM_EVENTS_EVENT_ID', config_get('zoom_events', 'event_id'));
define('ZOOM_EVENTS_TICKET_TYPE_ID', config_get('zoom_events', 'ticket_type_id'));
// Tickets are registered as zoomlogin+<badge>@<domain> so members' own
// addresses aren't shared with Zoom. Leave empty to use the member's email.
define('ZOOM_EVENTS_TICKET_EMAIL_DOMAIN', config_get('zoom_events', 'ticket_email_domain'));

define('DISCORD_CLIENT_ID', config_get('discord', 'client_id'));
// Invite code for the separate business meeting Discord server, the part after
// https://discord.gg/. Empty until it opens.
define('DISCORD_BM_INVITE', trim((string) config_get('discord', 'bm_invite')));

define('ALLOWLIST', array_map('trim', config_get('auth', 'allowlist', [])));
define('AUTH_PROVIDERS', array_map('trim', config_get('auth', 'providers', [])));

// The `oidc` login provider. Claim names are the userinfo claims that supply
// each member field; `role_map` maps a claim to the role it grants when the
// claim is truthy. See the README.
define('OIDC_PROVIDER_URL', config_get('oidc', 'provider_url'));
define('OIDC_CLIENT_ID', config_get('oidc', 'client_id'));
define('OIDC_SCOPES', array_map('trim', config_get('oidc', 'scopes', ['openid', 'email', 'profile'])));
define('OIDC_CLAIMS', array_map('trim', config_get('oidc', 'claims', [])) + ['email' => 'email', 'name' => 'name']);
define('OIDC_ROLE_MAP', array_map('trim', config_get('oidc', 'role_map', [])));
// Whether logging out of the portal also logs out at the provider
define('OIDC_PROVIDER_LOGOUT', in_array(strtolower(trim((string) config_get('oidc', 'provider_logout'))), ['1', 'true', 'yes', 'on'], true));
// Where a member signed in through the provider changes their password
define('OIDC_PASSWORD_CHANGE_URL', trim((string) config_get('oidc', 'password_change_url')));
// Only an explicit false turns certificate checking off
define('OIDC_VERIFY_TLS', !in_array(strtolower(trim((string) config_get('oidc', 'verify_tls', '1'))), ['', '0', 'false', 'no', 'off'], true));

// ConClár, the programme guide front end, may be hosted on another origin.
// allowed_origins[] lists the origins that may call the API with the session
// cookie, e.g. https://guide.example.com (no trailing slash).
define('CONCLAR_ALLOWED_ORIGINS', array_map('trim', config_get('conclar', 'allowed_origins', [])));
// Optional ConClár programme JSON, used to show item titles in the favourites report
define('CONCLAR_SCHEDULE_URL', trim((string) config_get('conclar', 'schedule_url')));
// The app ids a ConClár instance may store selections under, e.g. ['example2026']
define('CONCLAR_VALID_APP_IDS', []);
// The most items a member may have stored per app id
define('CONCLAR_MAX_ITEMS', 300);

define('SMTP_HOST', config_get('smtp', 'host'));
define('SMTP_PORT', (int) config_get('smtp', 'port', 25));
define('SMTP_USERNAME', config_get('smtp', 'username'));
define('SMTP_SECURE', config_get('smtp', 'secure'));

// $_SERVER key holding the client address when behind a proxy that sets it,
// e.g. HTTP_X_FORWARDED_FOR. Only set this if the proxy overwrites the header.
define('CLIENT_IP_HEADER', config_get('auth', 'client_ip_header'));

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
  // Drop any partial page output that is still buffered
  while (ob_get_level() > 0) {
    ob_end_clean();
  }
  if (!headers_sent()) {
    http_response_code(500);
  }
  require(__DIR__ . '/templates/error.php');
  exit();
}

set_exception_handler('exception_handler');

?>
