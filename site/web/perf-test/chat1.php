<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

$usernames = db_get_discord_usernames($_COOKIE['session']);

render_header(
  'Chat',
  'Join the Discord server for Glasgow 2024, A Worldcon for Our Futures.',
  ['Home' => '/', 'Chat online']
);
?>
<?php
render_footer();
?>