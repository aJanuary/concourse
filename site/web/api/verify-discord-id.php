<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
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

  render_header(
    'Verify Discord ID',
    'Verify Discord ID.',
    ['Home' => '/', 'Verify Discord ID']
  );

?>
  <article>
    <h3>Success!</h3>
    <p>Thank you, we have now associated this membership with the Discord account <?php echo htmlspecialchars($verification_result["username"]); ?>. You may close this tab.</p>
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
    <p>Are you sure you want to associate the membership for <?php echo htmlspecialchars($GLOBALS['app-data']['username']); ?> with the Discord account <?php echo htmlspecialchars($discord_id); ?>?</p>
    <form action="/api/verify-discord-id" method="post">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
      <button type="submit" class="button">Yes, this is my Discord account</button>
    </form>
  </article>
<?php
  
    render_footer();
}
?>