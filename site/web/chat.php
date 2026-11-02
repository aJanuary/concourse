<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

$usernames = db_get_discord_usernames($_COOKIE[SESSION_COOKIE_NAME]);

render_page('pages/chat.html.twig', [
  'title' => 'Chat',
  'description' => 'Join the Discord server for ' . CON_NAME,
  'breadcrumbs' => ['Home' => '/', 'Chat online'],
  'usernames' => $usernames,
  'authorize_url' => is_anonymous() ? null : discord_authorize_url(),
  'channel_url' => 'https://discord.com/channels/' . DISCORD_GUILD_ID . '/' . DISCORD_INVITE_CHANNEL_ID,
]);
