<?php

require_once(getenv('CONFIG_LIB_DIR') . '/api.php');

handle_request(function($data) {
  foreach ($data as $post) {
    db_upsert_discord_post($post['itemId'], $post['start'], $post['mins'], $post['roomId'], $post['postUrl']);
  }
  return ['result' => 'success'];
});