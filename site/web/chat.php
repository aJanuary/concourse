<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

$usernames = db_get_discord_usernames($_COOKIE['session']);

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Online Chat</h3>
  <p>We are using a platform called <a href="https://discord.com/" target="_blank">Discord</a> to host our online chat, both text and audio/video. There are apps for desktop and mobile, or you can use it in your browser.</p>
<?php
  if (empty($usernames)) {
    if (is_anonymous()) {
?>
      <p><a class="button" href="<?php echo make_login_link(); ?>">Log in to chat</a></p>
<?php
    } else {

?>
  <p><a class="button" target="_blank" href="https://discord.com/oauth2/authorize?client_id=<?php echo DISCORD_CLIENT_ID; ?>&response_type=code&redirect_uri=<?php echo urlencode(ROOT_URL) ?>%2Fchat_callback&scope=identify">Join the Discord server</a></p>
<?php
    }
  } else {
    if (count($usernames) === 1) {
?>
  <p>You have joined the Discord with the username <?php echo $usernames[0]; ?>.</p>
<?php
    } else {
?>
  <p>You have joined the Discord with the usernames:</p>
  <ul>
<?php
      foreach ($usernames as $username) {
?>
    <li><?php echo $username; ?></li>
<?php
      }
?>
  </ul>
<?php
    }
?>
  <p><a class="button" target="_blank" href="https://discord.com/channels/1214240728130256926/1214240728583118871">Go to Discord in your browser</a> or log in as <?php echo $usernames[0]; ?> in the <a href="https://discord.com/download">Discord app</a></p>
  <p><a target="_blank" href="https://discord.com/oauth2/authorize?client_id=<?php echo DISCORD_CLIENT_ID; ?>&response_type=code&redirect_uri=<?php echo urlencode(ROOT_URL) ?>%2Fchat_callback&scope=identify">Join the Discord server as a different user</a></p>
<?php
  }
?>
  <p>Your server nickname will be automatically set to your convention badge name. This is only visible to people in this server, and not in any other servers you might be in. You may change your nickname on this server if you wish, but we recommend keeping it to match your badge name to make it easier for people to find you.</p>

  <h4>Problems</h4>
  <p>If you have any problems, email <a href="mailto:<?php echo SUPPORT_EMAIL; ?>"><?php echo SUPPORT_EMAIL; ?></a> from the e-mail you signed up with and include your discord username, if you know it.</p>

  <h4>How to join the Discord Server</h4>
  <ol>
    <li>
      <p>Click the <kbd>Join the Discord Server</kbd> button above. This will open a new tab.</p>
    </li>
    <li>
      <p>If you are not logged in to Discord in your browser, you will need to log in. If you don't have a Discord account, you will need to create one by clicking the <kbd>Register</kbd> link. <a href="https://support.discord.com/hc/en-us/articles/360057027354-How-to-Login-to-your-Account" target="_blank">Need more help logging in?</a></p>
      <p><img src="/resources/discord/1-login.png" alt="Discord login screen"></p>
    </li>
    <li>
      <p>If you are already logged in, make sure you are logged in to the same account that you want to use to join the convention Discord. You can use the avatar and username to check which user you are logged in as. If you are logged in as the wrong user, click the <kbd>Not You?</kbd> link.</p>
      <p><img src="/resources/discord/2-check-user.png" alt="Discord OAuth permission screen with the avatar and username highlighted"></p>
    </li>
    <li>
      <p>Review the permissions we are requesting. We ask for your Discord username so that we can associate your Discord account with your convention membership. (Discord does not allow us to request this information without also requesting your avatar and banner)</p>
      <p><img src="/resources/discord/3-check-perms.png" alt="Discord OAuth permission screen with the permissions highlighted"></p>
    </li>
    <li>
      <p>Click the <kbd>Authorize</kbd> button.</p>
      <p><img src="/resources/discord/4-authorize.png" alt="Discord OAuth permission screen with the Authorize button highlighted"></p>
    </li>
    <li>
      <p>If you have the Discord app installed, it will open the Discord app. Otherwise, it will take you to the Discord website.</p>
    </li>
    <li>
      <p>A pop-up will appear inviting you to join the server. Click the <kbd>Accept Invite</kbd> button.</p>
      <p><img src="/resources/discord/5-join.png" alt="Discord server invite dialog with the Accept Invite button highlighted"></p>
    </li>
    <li>
      <p>Fill out the onboarding questions. These are aimed at making sure you see the correct channels. If any of the questions don't apply to you, you can always just click <kbd>Skip</kbd>.</p>
      <p><img src="/resources/discord/6-onboarding.png" alt="Discord onboarding questions"></p>
    </li>
    <li>
      <p>After completing the questions, you will be taken to the <kbd>Server Guide</kbd>. This has a list of tasks to help you get started with Discord. For example, the first one is <kbd>Check out the read-me!</kbd>. Click it to be taken to the Discord read-me.</p>
      <p><img src="/resources/discord/7-server-guide.png" alt="Discord server guide with the Check out the read-me! task highlighted"></p>
    </li>
    <li>
      <p>You can get back to the server guide to complete the other tasks, or re-read the Read-me, by clicking the <kbd>Server Guide</kbd> link in the channel list list on the left.</p>
      <p><img src="/resources/discord/8-server-guide-link.png" alt="Discord server guide link in the channel list"></p>
    </li>
  </ol>
  <h4>Help using Discord</h4>
  <p>You can read through our <a href="https://www.google.co.uk">Guide to using Discord</a> for more information on how to use Discord.</p>
</article>

<?php
render_footer();
?>