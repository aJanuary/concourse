<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-participant-guides');

render_header(
  'Convention guide',
  'Convention guide for Glasgow 2024',
  ['Home' => '/', 'Convention guide']
);
?>
<article>
  <h2>Convention guide</h2>
  <p>The convention guide will be published here in early August.</p>
</article>
<?php
render_footer();
?>