<?php
  require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/csrf.php');

  // chat_callback.php checks the state, so only a link from the portal can
  // link a Discord account
  function discord_authorize_url() {
    return 'https://discord.com/oauth2/authorize?' . http_build_query([
      'client_id' => DISCORD_CLIENT_ID,
      'response_type' => 'code',
      'redirect_uri' => ROOT_URL . '/chat_callback',
      'scope' => 'identify guilds.join',
      'state' => csrf_token('discord-oauth'),
    ]);
  }

  function request_discord_recheck_user($user_id) {
    require_config('discord', 'bot_token');
    $message_resp = api_call('https://discord.com/api/channels/' . DISCORD_API_CHANNEL_ID . '/messages', [
      'Authorization: Bot ' . config_get_secret('discord', 'bot_token'),
      'Content-Type: application/json'
    ], json_encode([
      'content' => json_encode([
        'action' => 'recheck-user',
        'user-id' => $user_id
      ]),
    ]));
    if (!array_key_exists('id', $message_resp)) {
      throw new Exception('Failed to send recheck message for ' . $user_id . '\n' . print_r($message_resp, true));
    }
  }
?>