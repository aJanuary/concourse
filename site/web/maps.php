<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Maps',
  'Maps of the various venues at ' . CON_NAME . '.',
  ['Home' => '/', 'Maps']
);
?>
<article>
  <h2>Maps</h2>
  <p>Maps of the venue, along with a list of rooms and their locations, will be available here closer to the convention.</p>
</article>
<?php
render_footer();
?>