<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (current_user_has_permission('see-newsletter-redirect')) {
  header('Location: ' . 'https://example.com/newsletter');
  exit;
}

render_header(
  'Newsletter',
  'Newsletter for ' . CON_NAME . '.',
  ['Home' => '/', 'Newsletter']
);
?>
<article>
  <h2>Newsletter</h2>
  <p>The convention newsletter will be published during the convention. It will keep you up to date with the latest announcements and information about what&apos;s happening the convention.</p>
  <p>In the meantime, you can check out our <a href="https://example.com/blog/">blog</a> for pre-con updates.</p>
</article>
<?php
render_footer();
?>