<?php
require_once('../../includes/config.php');
require_once('../../includes/session_auth.php');
require_once('../../includes/template.php');

$magic_link = db_get_magic_link($_COOKIE['session']);

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Stream and catch-up</h3>
  <p>We are using a platform called Ring Central Events to stream our programme items. You can watch the live streams of programme items, or watch the recorded videos afterwards. It works on both desktop and mobile.</p>
  <p><a class="button" target="_blank" href="<?php echo $magic_link; ?>">Go to Ring Central Events</a></p>
  <p><em>This link is tied to your membership. Please do not share it with other people.</em></p>
  <p>Thank you to <a href="https://glasgow2024.org/">Glasgow 2024</a> for donating the use of their Ring Central Events account for our convention.</p>
  <h4>How to use Ring Central Events</h4>
  <p>Instructions go here.</p>

</article>

<?php
render_footer();
?>