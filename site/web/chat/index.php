<?php
require_once('../../includes/config.php');
require_once('../../includes/session_auth.php');
require_once('../../includes/template.php');

$magic_link = db_get_magic_link($_COOKIE['session']);

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Online Chat</h3>
  <p>We are using a platform called <a href="https://discord.com/" target="_blank">Discord</a> to host our online chat, both text and audio/video. There are apps for desktop and mobile, or you can use it in your browser.</p>
  <p><a class="button" target="_blank" href="https://discord.com/oauth2/authorize?client_id=<?php echo DISCORD_CLIENT_ID; ?>&response_type=code&redirect_uri=<?php echo urlencode(ROOT_URL) ?>%2Fchat%2Fcallback%2F&scope=identify">Join the Discord server</a></p>
  
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
      <p>TODO: Instructions for going through the onboarding.</p>
    </li>
  </ol>
  <h4>Help using Discord</h4>
  <p>You can read through our <a href="https://www.google.co.uk">Guide to using Discord</a> for more information on how to use Discord.</p>
</article>

<?php
render_footer();
?>