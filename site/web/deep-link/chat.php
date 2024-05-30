<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (array_key_exists('item_id', $_GET)) {
  $item_id = $_GET['item_id'];
  $post = db_get_discord_post($item_id);

  if (!$post['post_url']) {
    render_header();
    ?>
    <a href="/" class="back">&lt; Back to member portal</a>
  
    <article>
      <h3>Unknown item</h3>
      <p>Sorry, we don&apos;t know about that item.</p>
    </article>
    <?php
    render_footer();
    exit;
  }  
} else if (array_key_exists('room_id', $_GET)) {
  $room_id = $_GET['room_id'];
  $post = db_get_discord_post_by_room_and_time($room_id, time());

  if (!$post['post_url']) {
    render_header();
    ?>
    <a href="/" class="back">&lt; Back to member portal</a>
  
    <article>
      <h3>Nothing scheduled</h3>
      <p>We don&apos;t think anything is happening in that room at the moment. Check back later.</p>
    </article>
    <?php
    render_footer();
    exit;
  }  
} else {
  die('Missing required query parameter "item_id" or "room_id"');
}

$usernames = db_get_discord_usernames($_COOKIE['session']);
if (!empty($usernames)) {
  header('Location: ' . $post['post_url']);
  exit;
}

render_header();
?>
  <a href="/" class="back">&lt; Back to member portal</a>

  <article>
    <h3>Join the Discord</h3>
    <p>Before we can take you to the Discord conversation for this item, you'll need to join the Discord.</p>
    <ol>
      <li><p>Follow the instructions on the <a href="/chat" target="_blank">chat page</a> to join the Discord.</p></li>
      <li><p>Once you have joined, click the button below to go directly to the post.</p><p><a class="button" href="<?php echo $post['post_url']; ?>">Discuss the item in Discord</a></p></li>
    </ol>
  </article>
<?php
render_footer();
?>