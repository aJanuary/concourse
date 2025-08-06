<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-participant-guides');

render_header(
  'Moderator guide',
  'Moderator guide for ' . CON_NAME . '.',
  ['Home' => '/', 'Guides' => '/guides/list', 'Moderator guide']
);
?>
<article class="instructions">
  <h2>Moderator guide</h2>
  <p>The moderator guide will be posted closer to the convention.
</article>
<?php
render_footer();
?>