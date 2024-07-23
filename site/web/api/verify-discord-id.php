<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/discord.php');


if (!array_key_exists('token', $_GET)) {
  throw new Exception('Missing token parameter');
}
$token = $_GET['token'];

if (array_key_exists('confirm', $_GET)) {
  $badge_no = get_current_user_badge_no();
  $verification_result = db_set_discord_id_from_token($badge_no, $token);
  if ($verification_result["result"] === "no-token") {
    throw new Exception('Invalid token');
  }

  if ($verification_result["result"] != "already-verified") {
    request_discord_recheck_user($verification_result["id"]);
  }

  render_header(
    'Verify Discord ID',
    'Verify Discord ID.',
    ['Home' => '/', 'Verify Discord ID']
  );

?>
  <article>
    <h3>Success!</h3>
    <p>Thank you, we have now associated this membership with the Discord account <?php htmlspecialchars($verification_result["username"]); ?>. You may close this tab.</p>
  </article>
<?php

  render_footer();
} else {
  $discord_id = db_get_discord_id_from_token($token);

  render_header(
    'Verify Discord ID',
    'Verify Discord ID.',
    ['Home' => '/', 'Verify Discord ID']
  );

?>
  <article>
    <h3>Verify Discord ID</h3>
    <p>Are you sure you want to associate the membership for <?php echo htmlspecialchars($_REQUEST['username']); ?> with the Discord account <?php echo htmlspecialchars($discord_id); ?>?</p>
    <p><a href="/api/verify-discord-id?token=<?php echo htmlspecialchars($token); ?>&confirm=true" class="button">Yes, this is my Discord account</a></p>
  </article>
<?php
  
    render_footer();
}
?>