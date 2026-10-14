<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

render_header(
  'Manage programme',
  'Manage programme.',
  ['Home' => '/', 'Manage programme']
);
?>
<article>
  <h2>Manage programme</h2>
  <ul>
    <li><a href="/admin/programme/replay/list">Manage replays</a></li>
    <li><a href="/admin/programme/zoom/edit">Manage Zoom link</a></li>
    <li><a href="/admin/programme/stages/list">Manage stages</a></li>
    <li><a href="/admin/programme/sessions/list">Manage sessions</a></li>
    <li><a href="/admin/programme/stream-urls/list">Manage stream URLs</a></li>
    <li><a href="/admin/programme/discord-posts/list">Manage Discord posts</a></li>
    <li><a href="/admin/programme/signups/list">Manage sign-ups</a></li>
  </ul>
</article>

<?php
render_footer();
?>