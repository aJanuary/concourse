<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Current user',
  'Who is logged in to ' . CON_NAME . '.',
  ['Home' => '/', 'Current user']
);
?>
<article>
  <h2>Is this you?</h2>
  <p>You are currently logged in as <?php echo htmlspecialchars(get_current_user_name()); ?>.</p>
  <p><a href="/" class="button">Yes, this is me</a></p>
  <p><a href="/logout">No, log them out</a></p>
</article>
<?php
render_footer();
