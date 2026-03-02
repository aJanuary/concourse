<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!array_key_exists('item_id', $_GET)) {
  die('Missing required query parameter "item_id"');
}

$item_id = $_GET['item_id'];

if (is_anonymous()) {
  render_header(
    'Go to an item replay',
    'Log in to view replay.',
    ['Home' => '/', 'Go to an item replay']
  );
?>
  <article>
    <h2>Log in to view replay</h2>
    <p>You need to be logged in to view this replay. Please log in to the members portal to continue.</p>
    <p><a href="<?php echo make_login_link(); ?>" class="button">Log in</a></p>
  </article>
<?php
  render_footer();
  exit;
}

$replay = db_get_replay($item_id);

if (!$replay['replay_url']) {
  render_header(
    'Go to an item replay',
    'Replay not available.',
    ['Home' => '/', 'Go to an item replay']
  );
  ?>
  <article>
    <h2>No replay available yet</h2>
    <p>Replay isn&apos;t available for this item yet. We&apos;re working to get replays up as soon as we can, so check back soon.</p>
  </article>
  <?php
  render_footer();
  exit;
}

header('Location: ' . $replay['replay_url']);
exit;
?>
