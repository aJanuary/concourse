<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

$code = $_GET['code'];
if (!$code) {
  error_log("No code in chat_callback.php for " . get_current_user_badge_no() . ": " . print_r($_GET, true));

  if ($_GET['error'] == 'access_denied') {
    render_header();
?>
  <article>
    <h3>Something went wrong</h3>
    <p>It looks like you did not click "Authorize" to give us permission to access and store your Discord username.</p>
    <p>For safety reasons, we require this to be able to associate your membership with your Discord account.</p>
    <p>Please go back to the <a href="/chat">chat page</a> and try again.</p>
    <p>If you think you are seeing this page in error, please contact <a href="mailto:<?php echo EMAIL; ?>?subject=Problem with member portal&body=Problem: Access denied in chat_callback.php"><?php echo EMAIL; ?></a>.</p>
  </article>
<?php
    render_footer();
    exit();
  } else {
    die("Sorry, something went wrong. Please try again. If the problem persists, contact <a href=\"mailto:" . EMAIL . "?subject=Problem with member portal&body=Problem: No code in chat_callback.php\">". EMAIL . "</a>");
  }
}

// Exchange code for access token
$token_resp = api_call('https://discord.com/api/oauth2/token', [
  'Content-Type: application/x-www-form-urlencoded'
], http_build_query([
  'client_id' => DISCORD_CLIENT_ID,
  'client_secret' => DISCORD_CLIENT_SECRET,
  'grant_type' => 'authorization_code',
  'code' => $code,
  'redirect_uri' => ROOT_URL . '/chat_callback',
  'scope' => 'identify'
]));
$access_token = $token_resp['access_token'];
if (!$access_token) {
  error_log("Missing access_token for " . get_current_user_badge_no() . ": " . print_r($token_resp, true));
  die("Sorry, something went wrong. Please try again. If the problem persists, contact <a href=\"mailto:" . EMAIL . "?subject=Problem with member portal&body=Problem: No access token in chat_callback.php\">". EMAIL . "</a>");
}

// Store username
$me_resp = api_call('https://discord.com/api/users/@me', [
  'Authorization: Bearer ' . $access_token
]);

$discord_id = $me_resp['id'];
$discord_username = $me_resp['username'];

if (!$discord_id || !$discord_username) {
  error_log("Missing discord_id or discord_username for " . get_current_user_badge_no() . ": " . print_r($me_resp, true));
  die("Sorry, something went wrong. Please try again. If the problem persists, contact <a href=\"mailto:" . EMAIL . "?subject=Problem with member portal&body=Problem: No Discord ID or username in chat_callback.php\">". EMAIL . "</a>");
}

db_set_discord_id(get_current_user_badge_no(), $discord_id, $discord_username);

// Create invite
$invite_resp = api_call('https://discord.com/api/channels/' . DISCORD_INVITE_CHANNEL_ID . '/invites', [
  'Authorization: Bot ' . DISCORD_BOT_TOKEN,
  'Content-Type: application/json'
], json_encode([
  'max_age' => 86400,
  'max_uses' => 1,
  'unique' => true
]));
$invite_code = $invite_resp['code'];

if (!$invite_code) {
  error_log("Missing invite_code for " . get_current_user_badge_no() . ": " . print_r($invite_resp, true));
  die("Sorry, something went wrong. Please try again. If the problem persists, contact <a href=\"mailto:" . EMAIL . "?subject=Problem with member portal&body=Problem: No invite code in chat_callback.php\">". EMAIL . "</a>");
}

header('Location: https://discord.gg/' . $invite_code);

?>