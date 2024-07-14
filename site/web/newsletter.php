<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Newsletter',
  'Newsletter for Glasgow 2024',
  ['Home' => '/', 'Newsletter']
);
?>
<article>
  <h2>Newsletter</h2>
  <p>The convention newsletter will be published during the convention. It will keep you up to date with the latest announcements and information about what&apos;s happening the convention.</p>
  <p>In the meantime, you can check out our <a href="https://glasgow2024.org/blog/">blog</a> for pre-con updates.</p>
</article>
<?php
render_footer();
?>