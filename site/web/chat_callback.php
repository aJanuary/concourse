<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

// $outcome is the part of the template to show: access-denied, not-linked or
// failed. $problem goes in the body of the email to support.
function render_chat_problem($outcome, $problem): never {
  render_page('pages/chat_callback.html.twig', [
    'outcome' => $outcome,
    'problem' => $problem,
  ]);
  exit;
}

$code = $_GET['code'];
if (!$code) {
  error_log("No code in chat_callback.php for " . get_current_user_badge_no() . ": " . print_r($_GET, true));

  if ($_GET['error'] == 'access_denied') {
    render_chat_problem('access-denied', 'Access denied in chat_callback.php');
  } else {
    render_chat_problem('failed', 'No code in chat_callback.php');
  }
}

// Without this another site could send a member here with a code for its own
// Discord account, linking that account to the member
if (!csrf_token_valid($_GET['state'] ?? null, 'discord-oauth')) {
  render_chat_problem('not-linked', 'Invalid state in chat_callback.php');
}

require_config('discord', 'client_id', 'client_secret', 'bot_token');

// Exchange code for access token
$token_resp = api_call('https://discord.com/api/oauth2/token', [
  'Content-Type: application/x-www-form-urlencoded'
], http_build_query([
  'client_id' => DISCORD_CLIENT_ID,
  'client_secret' => config_get_secret('discord', 'client_secret'),
  'grant_type' => 'authorization_code',
  'code' => $code,
  'redirect_uri' => ROOT_URL . '/chat_callback',
  'scope' => 'identify guilds.join'
]));
$access_token = $token_resp['access_token'];
if (!$access_token) {
  error_log("Missing access_token for " . get_current_user_badge_no() . ": " . print_r($token_resp, true));
  render_chat_problem('failed', 'No access token in chat_callback.php');
}

// Store username
$me_resp = api_call('https://discord.com/api/users/@me', [
  'Authorization: Bearer ' . $access_token
]);

$discord_id = $me_resp['id'];
$discord_username = $me_resp['username'];

if (!$discord_id || !$discord_username) {
  error_log("Missing discord_id or discord_username for " . get_current_user_badge_no() . ": " . print_r($me_resp, true));
  render_chat_problem('failed', 'No Discord ID or username in chat_callback.php');
}

db_set_discord_id(get_current_user_badge_no(), $discord_id, $discord_username);


// Join server
$join_resp = api_call('https://discord.com/api/guilds/' . DISCORD_GUILD_ID . '/members/' . $discord_id, [
  'Authorization: Bot ' . config_get_secret('discord', 'bot_token'),
  'Content-Type: application/json'
], json_encode([
  'access_token' => $access_token,
  'nick' => get_current_user_name(),
]), 'PUT');

// Note that we don't actually care about the response. If it fails, we'll just hope the invite path works.
error_log("Join response for " . get_current_user_badge_no() . ": " . print_r($join_resp, true));

// Create invite
$invite_resp = api_call('https://discord.com/api/channels/' . DISCORD_INVITE_CHANNEL_ID . '/invites', [
  'Authorization: Bot ' . config_get_secret('discord', 'bot_token'),
  'Content-Type: application/json'
], json_encode([
  'max_age' => 86400,
  'max_uses' => 1,
  'unique' => true
]));

error_log("Invite response for " . get_current_user_badge_no() . ": " . print_r($invite_resp, true));

$invite_code = $invite_resp['code'];

if (!$invite_code) {
  error_log("Missing invite_code for " . get_current_user_badge_no() . ": " . print_r($invite_resp, true));
  render_chat_problem('failed', 'No invite code in chat_callback.php');
}

header('Location: https://discord.gg/' . $invite_code);
