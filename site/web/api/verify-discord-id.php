<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/discord.php');


$token = $_POST['token'] ?? $_GET['token'] ?? null;
if ($token === null) {
  throw new Exception('Missing token parameter');
}

// Confirming is a POST so that session_auth.php checks its CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $badge_no = get_current_user_badge_no();
  $verification_result = db_set_discord_id_from_token($badge_no, $token);
  if ($verification_result["result"] === "no-token") {
    throw new Exception('Invalid token');
  }

  if ($verification_result["result"] != "already-verified") {
    request_discord_recheck_user($verification_result["id"]);
  }

  render_page('pages/api/verify-discord-id.html.twig', [
    'verified_username' => $verification_result['username'],
  ]);
} else {
  render_page('pages/api/verify-discord-id.html.twig', [
    'verified_username' => null,
    'discord_id' => db_get_discord_id_from_token($token),
    'token' => $token,
  ]);
}
