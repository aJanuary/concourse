<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-participant-guides');

render_header(
  'Online participant guide',
  'Online participant guide for ' . CON_NAME . '.',
  ['Home' => '/', 'Guides' => '/guides/list', 'Online participant guide']
);
?>
<article class="instructions">
  <h2>Online participant guide</h2>
  <p>The online programme participant guide will be posted closer to the convention.
</article>
<?php
render_footer();
?>