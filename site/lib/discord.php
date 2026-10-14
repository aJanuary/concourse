<?php
  require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');

  function request_discord_recheck_user($user_id) {
    require_config('discord', 'bot_token');
    $message_resp = api_call('https://discord.com/api/channels/' . DISCORD_API_CHANNEL_ID . '/messages', [
      'Authorization: Bot ' . DISCORD_BOT_TOKEN,
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