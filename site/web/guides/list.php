<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-participant-guides');

render_header(
  'Participant guides',
  'Participant guides for ' . CON_NAME . '.',
  ['Home' => '/', 'Guides']
);
?>
<article>
<h2>Guides</h2>
<ul>
  <li><a href="/guides/participant">Participant guide</a> - For all programme participants</li>
  <li><a href="/guides/moderator">Moderator guide</a> - For programme moderators</li>
  <li><a href="/guides/online">Online guide</a> - For online participants</li>
</ul>
</article>
<?php
render_footer();
?>