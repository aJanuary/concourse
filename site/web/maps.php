<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Maps',
  'Maps of the various venues at Glasgow 2024',
  ['Home' => '/', 'Maps']
);
?>
<article>
  <h2>Maps</h2>
  <ul class="maps">
    <li><a href="/resources/maps/sec-campus.pdf"><img src="/resources/maps/sec-campus-thumb.png" alt="">Scottish Event Campus</a></li>
    <li><a href="/resources/maps/sec.pdf"><img src="/resources/maps/sec-thumb.png" alt="">Scottish Event Campus Venues</a></li>
    <li><a href="/resources/maps/hall4.jpg"><img src="/resources/maps/hall4-thumb.png" alt="">Hall 4</a></li>
    <li><a href="/resources/maps/hall5.jpg"><img src="/resources/maps/hall5-thumb.png" alt="">Hall 5</a></li>
    <li><a href="/resources/maps/village.pdf"><img src="/resources/maps/village-thumb.png" alt="">The Village</a></li>
    <li><a href="/resources/maps/crowne-plaza.pdf"><img src="/resources/maps/crowne-plaza-thumb.png" alt="">Crowne Plaza</a></li>
  </ul>
</article>
<?php
render_footer();
?>