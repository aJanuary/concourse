<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (current_user_has_permission('see-guide-redirect')) {
  header('Location: ' . 'https://guide.glasgow2024.org/');
  exit;
}

render_header(
  'Programme guide',
  'Programme guide for Glasgow 2024',
  ['Home' => '/', 'Programme guide']
);
?>
<article>
  <h2>Programme guide</h2>
  <p>The full programme will be announced in early August.</p>
  <p>In the meantime, you can see some key items with dates and times on our <a target="_blank" href="https://glasgow2024.org/highlights/">highlights</a> page, and the sort of things we are planning in our <a href="https://glasgow2024.org/whats-on/programme/programme-sampler/" target="_blank">programme sampler</a>.</p>
</article>
<?php
render_footer();
?>