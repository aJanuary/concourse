<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/discord.php');

render_header(
  'Verify Discord ID',
  'Verify Discord ID.',
  ['Home' => '/', 'Verify Discord ID']
);

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
  request_discord_recheck_user($verification_result["id"]);
}

?>
  <article>
    <h3>Success!</h3>
    <p>Thank you, we have now associated this membership with the Discord account <?php echo $verification_result["username"]; ?>. You may close this tab.</p>
  </article>
<?php

render_footer();
?>