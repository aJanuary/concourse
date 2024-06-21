<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Discord instructions',
  'Instructions on how to join the Glasgow 2024 Discord server.',
  ['Home' => '/', 'Online Chat' => '/chat', 'How to join the Discord Server']
);
?>
<article>
  <h2>How to join the Discord Server</h2>
  <ol class="instructions">
    <li>
      <p>Click the <kbd>Join the Discord Server</kbd> button. This will open a new tab.</p>
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
</article>
<?php
render_footer();
?>