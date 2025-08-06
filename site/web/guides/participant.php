<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-participant-guides');

render_header(
  'Participant guide',
  'Participant guide for ' . CON_NAME . '.',
  ['Home' => '/', 'Guides' => '/guides/list', 'Participant guide']
);
?>
<article class="instructions">
  <h2>Participant guide</h2>
  <p>This is a guide for all programme participants.</p>
  <p>It is complemented by the <a href="/guides/moderator">Panel Moderator Guide</a> and the <a href="/guides/online">Online Programme Participant Guide</a>.</p> 
  <p>The participant guide will be posted closer to the convention.
</article>
<?php
render_footer();
?>