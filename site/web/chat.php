<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

$usernames = db_get_discord_usernames($_COOKIE['session']);

render_header(
  'Chat',
  'Join the Discord server for Glasgow 2024, A Worldcon for Our Futures.',
  ['Home' => '/', 'Online Chat']
);
?>

<article>
  <h2>Online Chat</h2>
  <p>We are using a platform called <a href="https://discord.com/" target="_blank">Discord</a> to host our online chat, both text and audio/video. There are apps for desktop and mobile, or you can use it in your browser.</p>
<?php
  if (empty($usernames)) {
    if (is_anonymous()) {
?>
      <p><a class="button" href="<?php echo make_login_link(); ?>">Log in to chat</a></p>
<?php
    } else {

?>
  <p><a class="button" target="_blank" href="https://discord.com/oauth2/authorize?client_id=<?php echo DISCORD_CLIENT_ID; ?>&response_type=code&redirect_uri=<?php echo urlencode(ROOT_URL) ?>%2Fchat_callback&scope=identify+guilds.join">Join the Discord server</a></p>
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
  <p><a class="button" target="_blank" href="https://discord.com/channels/<?php echo DISCORD_GUILD_ID . '/' . DISCORD_INVITE_CHANNEL_ID ?>">Go to Discord in your browser</a> or log in as <?php echo $usernames[0]; ?> in the <a href="https://discord.com/download">Discord app</a></p>
  <p><a target="_blank" href="https://discord.com/oauth2/authorize?client_id=<?php echo DISCORD_CLIENT_ID; ?>&response_type=code&redirect_uri=<?php echo urlencode(ROOT_URL) ?>%2Fchat_callback&scope=identify+guilds.join">Join the Discord server as a different user</a></p>
<?php
  }
?>
  <p>Your server nickname will be automatically set to your convention badge name. This is only visible to people in this server, and not in any other servers you might be in. You may change your nickname on this server if you wish, but we recommend keeping it to match your badge name to make it easier for people to find you.</p>

  <h3>Problems</h3>
  <p>Read the <a href="/discord-instructions" target="_blank">instructions on how to join the Discord server</a>.</p>
  <p>If you have any problems, email <a href="mailto:<?php echo SUPPORT_EMAIL; ?>"><?php echo SUPPORT_EMAIL; ?></a> from the e-mail you signed up with and include your discord username, if you know it.</p>
  <p>You can read through our <a href="https://www.google.co.uk">Guide to using Discord</a> for more information on how to use Discord.</p>
</article>

<?php
render_footer();
?>