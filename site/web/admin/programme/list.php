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
    <li><a href="/admin/programme/replay/list">Manage <abbr title="RingCentral Events">RCE</abbr> replays</a></li>
    <li><a href="/admin/programme/zoom/edit">Manage Zoom link</a></li>
    <li><a href="/admin/programme/stages/list">Manage <abbr title="RingCentral Events">RCE</abbr> stages</a></li>
    <li><a href="/admin/programme/sessions/list">Manage <abbr title="RingCentral Events">RCE</abbr> sessions</a></li>
    <li><a href="/admin/programme/discord-posts/list">Manage Discord posts</a></li>
  </ul>
</article>

<?php
render_footer();
?>