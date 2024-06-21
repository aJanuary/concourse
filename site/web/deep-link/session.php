<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/deep_link.php');

if (!array_key_exists('item_id', $_GET)) {
  die('Missing required query parameter "item_id"');
}

$item_id = $_GET['item_id'];
$session = db_get_prog_session($item_id);

if (!$session['rce_url']) {
  render_header("Go to a session", "Unknown item.");
  ?>
  <a href="/" class="back">&lt; Back to member portal</a>

  <article>
    <h2>Unknown item</h2>
    <p>Sorry, we don&apos;t know about that item.</p>
  </article>
  <?php
  render_footer();
  exit;
}

render_rce_wizard("Go to a session", $session['rce_url']);
?>