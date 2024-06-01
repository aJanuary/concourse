<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!array_key_exists('item_id', $_GET)) {
  die('Missing required query parameter "item_id"');
}

$item_id = $_GET['item_id'];
$replay = db_get_replay($item_id);

if (!$replay['replay_url']) {
  render_header();
  ?>
  <a href="/" class="back">&lt; Back to member portal</a>

  <article>
    <h3>No replay available yet</h3>
    <p>Replay isn&apos;t available for this item yet. We&apos;re working to get replays up as soon as we can, so check back soon.</p>
  </article>
  <?php
  render_footer();
  exit;
}

if (array_key_exists('seen-rce-invite', $_COOKIE)) {
  header('Location: ' . $replay['replay_url']);
  exit;
}

render_header();
?>
  <a href="/" class="back">&lt; Back to member portal</a>

  <article>
    <h3>Log in to RingCentral Events</h3>
    <p>Before we can take you to the item in RingCentral Events, you'll need to first visit the landing page of RingCentral Events on this device.</p>
    <ol>
      <li><p>Follow the instructions on the <a href="/stream" target="_blank">stream page</a> to go to RingCentral Events landing page.</p></li>
      <li><p>Once you have visited the landing page, click the button below to go directly to the item.</p><p><a class="button" href="<?php echo $replay['replay_url']; ?>">Go to the item in RingCentral Events</a></p></li>
    </ol>
  </article>
<?php
render_footer();
?>