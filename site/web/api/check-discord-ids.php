<?php

require_once(getenv('CONFIG_LIB_DIR') . '/api.php');

handle_request(function($data) {
  $recorded_discord_info = db_get_all_discord_info();
  $response = [];

  foreach ($data["discordUsers"] as $discord_user) {
    $discord_id = $discord_user["id"];
    $discord_username = $discord_user["username"];
    if (isset($recorded_discord_info[$discord_id])) {
      $response[$discord_id] = $recorded_discord_info[$discord_id];
    } else {
      $token = base64_encode(random_bytes(30));
      db_insert_discord_token($token, $discord_id, $discord_username);
      $url = ROOT_URL . "/api/verify-discord-id?token=" . urlencode($token);
      $response[$discord_id] = $url;
    }
  }

  return $response;
});

?>