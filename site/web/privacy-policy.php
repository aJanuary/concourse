<?php
$_REQUEST['allow_implicit_anonymous'] = 'always';
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Privacy policy',
  'An explanation of the cookies we set and what they are used for',
  ['Home' => '/', 'Privacy policy']
);
?>
<article>
  <h2>Privacy policy</h2>
  <p>The privacy policy will be updated closed to the convention.</p>
</article>
<?php
render_footer();
?>