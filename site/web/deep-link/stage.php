<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/deep_link.php');

if (!array_key_exists('room_id', $_GET)) {
  die('Missing required query parameter "room_id"');
}

$room_id = $_GET['room_id'];
$stage = db_get_stage($room_id);

if (!$stage['viewer_url']) {
  render_header("Go to a stage", "Unknown item.");
  ?>
  <a href="/" class="back">&lt; Back to member portal</a>

  <article>
    <h3>Unknown room</h3>
    <p>Sorry, we don&apos;t know about that room.</p>
  </article>
  <?php
  render_footer();
  exit;
}

render_rce_wizard("Go to a stage", $stage['viewer_url']);
?>