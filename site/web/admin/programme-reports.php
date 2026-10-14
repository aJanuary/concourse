<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-programme-reports');

render_header(
  'Programme reports',
  'Reports about programme item favourites.',
  ['Home' => '/', 'Programme reports']
);
?>
<article>
  <h2>Programme reports</h2>
  <ul>
    <li><a href="/admin/programme-reports/favourites">Programme item favourites</a></li>
  </ul>
</article>
<?php
render_footer();
?>
