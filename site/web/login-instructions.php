<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = 'always';
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Log in instructions',
  'Instructions on how to log in to the ' . CON_SHORT_NAME . ' member portal.',
  ['Home' => '/', 'How to log in']
);
?>
<article>
  <h2>How to log in</h2>
  <p>We&apos;re still working on this page.</p>
  <p>If you are involved in testing the site, this is probably fine. If you are not, please let us know.</p>
</article>

<?php
render_footer();
?>
