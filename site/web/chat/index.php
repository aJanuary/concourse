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
  <p>We are using a platform called <a href="https://discord.com/">Discord</a> to host our online chat, both text and audio/video. There are apps for desktop and mobile, or you can use it in your browser.</p>
  <p><a class="button" target="_blank" href="https://discord.com/oauth2/authorize?client_id=<?php echo DISCORD_CLIENT_ID; ?>&response_type=code&redirect_uri=<?php echo urlencode(ROOT_URL) ?>%2Fchat%2Fcallback%2F&scope=identify">Join the Discord server</a></p>
  
  <h4>How to use Discord</h4>
  <p>Instructions go here.</p>
</article>

<?php
render_footer();
?>