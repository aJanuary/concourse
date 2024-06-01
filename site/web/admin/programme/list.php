<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Manage programme</h3>
  <ul>
    <li><a href="/admin/programme/replay/list">Manage RCE replays</a></li>
    <li><a href="/admin/programme/zoom/edit">Manage Zoom link</a></li>
    <li><a href="/admin/programme/stages/list">Manage RCE stages</a></li>
    <li><a href="/admin/programme/sessions/list">Manage RCE sessions</a></li>
    <li><a href="/admin/programme/discord-posts/list">Manage Discord posts</a></li>
  </ul>
</article>

<?php
render_footer();
?>