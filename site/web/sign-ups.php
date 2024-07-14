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
  <p>Most of the programme you can just turn up to the room to watch, but some items have limited space and will require signing up in advance.</p>
  <p>We will be opening sign-ups on 6<sup>th</sup> August 2024.</p>
</article>
<?php
render_footer();
?>