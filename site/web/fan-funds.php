<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Fan funds',
  'Vote for fan funds at ' . CON_NAME . '.',
  ['Home' => '/', 'Fan funds']
);
?>
<article>
  <h2>Fan funds</h2>
  <p>Fan fund voting information will be available here closer to the convention.</p>
</article>
<?php
render_footer();
?>
