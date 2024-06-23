<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Site map',
  'A list of the pages on the portal.',
  ['Home' => '/', 'Site map']
);
?>
<article>
  <h2>Site map</h2>
  <ul>
    <li><p><a href="/">Home</a></p></li>
    <li>
      <p><a href="/chat">Chat</a></p>
      <ul>
        <li><p><a href="/discord-instructions">How to join the Discord Server</a></p></li>
      </ul>
    </li>
    <li><p><a href="/cookie-policy">Cookie policy</a></p></li>
    <li><p><a href="/help">Help</a></p></li>
    <li><p><a href="/login-instructions">Log in instructions</a></p></li>
    <li><p><a href="/site-map">Site map</a></p></li>
    <li><p><a href="/stream">Stream</a></p></li>
    <?php
    if (current_user_has_permission('manage-roles')) {
    ?>
    <li><p><a href="/admin/roles/list">Manage roles</a></p></li>
    <?php
    }
    ?>
    <?php
    if (current_user_has_permission('manage-discord-ids')) {
    ?>
    <li><p><a href="/admin/discord">Manage discord ids</a></p></li>
    <?php
    }
    ?>
    <?php
    if (current_user_has_permission('manage-programme')) {
    ?>
    <li>
      <p><a href="/admin/programme/list">Manage programme</a></p>
      <ul>
        <li><p><a href="/admin/programme/discord-posts/list">Manage Discord posts</a></p></li>
        <li><p><a href="/admin/programme/replay/list">Manage replay</a></p></li>
        <li><p><a href="/admin/programme/sessions/list">Manage sessions</a></p></li>
        <li><p><a href="/admin/programme/stages/list">Manage stages</a></p></li>
        <li><p><a href="/admin/programme/zoom/list">Manage Zoom link</a></p></li>
      </ul>
    </li>
    <?php
    }
    ?>
  </ul>
</article>
<?php
render_footer();
?>