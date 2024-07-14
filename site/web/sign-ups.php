<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Sign-ups',
  'Sign-up for programme items at Glasgow 2024',
  ['Home' => '/', 'Sign-ups']
);
?>
<article>
  <h2>Sign-ups</h2>
  <p>Some programme items at Glasgow 2024 require sign-up in advance. We will be opening up the sign-ups closer to the convention.</p>
</article>
<?php
render_footer();
?>