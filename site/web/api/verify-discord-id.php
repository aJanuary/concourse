<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');

render_header("Verify Discord ID.");

$token = $_GET['token'];
if (!$token) {
  throw new Exception('Missing token parameter');
}

$badge_no = get_current_user_badge_no();
$verification_result = db_set_discord_id_from_token($badge_no, $token);
if ($verification_result["result"] === "no-token") {
  throw new Exception('Invalid token');
}

if ($verification_result["result"] != "alread-verified") {
  $message_resp = api_call('https://discord.com/api/channels/' . DISCORD_API_CHANNEL_ID . '/messages', [
    'Authorization: Bot ' . DISCORD_BOT_TOKEN,
    'Content-Type: application/json'
  ], json_encode([
    'content' => json_encode([
      'action' => 'recheck-user',
      'user-id' => $verification_result["id"]
    ]),
  ]));
  if (!array_key_exists('id', $message_resp)) {
    throw new Exception('Failed to send recheck message for ' . $verification_result["id"] . '\n' . print_r($message_resp, true));
  }
}

?>
  <a href="/" class="back">&lt; Back to member portal</a>
  <article>
    <h3>Success!</h3>
    <p>Thank you, we have now associated this membership with the Discord account <?php echo $verification_result["username"]; ?>. You may close this tab.</p>
  </article>
<?php

render_footer();
?>