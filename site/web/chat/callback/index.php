<?php

require_once('../../../includes/config.php');
require_once('../../../includes/db.php');
require_once('../../../includes/session_auth.php');
require_once('../../../includes/requests.php');

$code = $_GET['code'];

// Exchange code for access token
$token_resp = api_call('https://discord.com/api/oauth2/token', [
  'Content-Type: application/x-www-form-urlencoded'
], http_build_query([
  'client_id' => DISCORD_CLIENT_ID,
  'client_secret' => DISCORD_CLIENT_SECRET,
  'grant_type' => 'authorization_code',
  'code' => $code,
  'redirect_uri' => ROOT_URL . '/chat/callback/',
  'scope' => 'identify'
]));
$access_token = $token_resp['access_token'];

// Store username
$me_resp = api_call('https://discord.com/api/users/@me', [
  'Authorization: Bearer ' . $access_token
]);

$discord_id = $me_resp['id'];
$discord_username = $me_resp['username'];

db_set_discord_id(get_current_user_badge_no(), $discord_id, $discord_username);

// Create invite
$invite_resp = api_call('https://discord.com/api/channels/' . DISCORD_CHANNEL_ID . '/invites', [
  'Authorization: Bot ' . DISCORD_BOT_TOKEN,
  'Content-Type: application/json'
], json_encode([
  'max_age' => 86400,
  'max_uses' => 1,
  'unique' => true
]));
$invite_code = $invite_resp['code'];

header('Location: https://discord.gg/' . $invite_code);

?>