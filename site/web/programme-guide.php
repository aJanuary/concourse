<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Programme guide',
  'Programme guide for Glasgow 2024',
  ['Home' => '/', 'Programme guide']
);
?>
<article>
  <h2>Programme guide</h2>
  <p>The full programme will be announced in early August. In the meantime, you can see the sort of things we are planning in our <a href="https://glasgow2024.org/whats-on/programme/programme-sampler/" target="_blank">programme sampler</a>.</p>
</article>
<?php
render_footer();
?>