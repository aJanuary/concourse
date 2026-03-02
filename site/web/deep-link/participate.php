<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (is_anonymous()) {
  render_header(
    'Participate in an item',
    'Log in to participate in an item.',
    ['Home' => '/', 'Participate in an item']
  );
?>
  <article>
    <h2>Log in to participate in an item</h2>
    <p>You need to be logged in to participate in an item. Please log in to the members portal to continue.</p>
    <p><a href="<?php echo make_login_link(); ?>" class="button">Log in</a></p>
  </article>
<?php
  render_footer();
  exit;
}

header('Location: ' . db_get_zoom_url());
exit;
?>
