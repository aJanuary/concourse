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
    <li><a href="/resources/maps/sec-campus.pdf"><img src="/resources/maps/sec-campus-thumb.webp" width="390" height="195" alt="">Scottish Event Campus</a></li>
    <li><a href="/resources/maps/sec2.pdf"><img src="/resources/maps/sec-thumb.webp" width="390" height="195" alt="">Scottish Event Campus Venues</a></li>
    <li><a href="/resources/maps/hall4-v2.pdf"><img src="/resources/maps/hall4-thumb.webp" width="390" height="195" alt="">Hall 4</a></li>
    <li><a href="/resources/maps/hall5.pdf"><img src="/resources/maps/hall5-thumb.webp" width="390" height="195" alt="">Hall 5</a></li>
    <li><a href="/resources/maps/village.pdf"><img src="/resources/maps/village-thumb.webp" width="390" height="195" alt="">The Village</a></li>
    <li><a href="/resources/maps/crowne-plaza.pdf"><img src="/resources/maps/crowne-plaza-thumb.webp" width="390" height="195" alt="">Crowne Plaza</a></li>
  </ul>
  <h3>Programme rooms</h3>
  <ul>
    <li>Alsh 1 &ndash; <a href="/resources/maps/sec2.pdf">SEC - Ground floor</a></li>
    <li>Alsh 2 &ndash; <a href="/resources/maps/sec2.pdf">SEC - Ground floor</a></li>
    <li>ARC Seminar Suite &ndash; <a href="https://maps.app.goo.gl/iF5VT2dfFaNpXnDw6">University of Glasgow campus</a></li>
    <li>ARC Studio Space &ndash; <a href=https://maps.app.goo.gl/iF5VT2dfFaNpXnDw6>University of Glasgow campus</a></li>
    <li>Argyll 1 &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - Ground floor</a></li>
    <li>Argyll 2 &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - Ground floor</a></li>
    <li>Argyll 3 &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - Ground floor</a></li>
    <li>Barra &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - First floor</a></li>
    <li>Carron &ndash; <a href="/resources/maps/sec2.pdf">SEC - First floor</a></li>
    <li>Castle 1 &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - First floor</a></li>
    <li>Castle 2 &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - First floor</a></li>
    <li>Castle 3 &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - First floor</a></li>
    <li>Clyde Auditorium &ndash; <a href="/resources/maps/sec2.pdf">SEC Armadillo</a></li>
    <li>Dochart 1 &ndash; <a href="/resources/maps/sec2.pdf">SEC - First floor</a></li>
    <li>Dochart 2 &ndash; <a href="/resources/maps/sec2.pdf">SEC - First floor</a></li>
    <li>Executive Boardroom (aka E.B.) &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - First floor</a></li>
    <li>Forth &ndash; <a href="/resources/maps/sec2.pdf">SEC Armadillo - Ground level</a></li>
    <li>Gala &ndash; <a href="/resources/maps/sec2.pdf">SEC Armadillo - Upper level</a></li>
    <li>Hall 1 &ndash; <a href="/resources/maps/sec2.pdf">SEC - Ground floor</a></li>
    <li>Hall 2 &ndash; <a href="/resources/maps/sec2.pdf">SEC - Ground floor</a></li>
    <li>Hall 3 &ndash; <a href="/resources/maps/sec2.pdf">SEC - Ground floor</a></li>
    <li>Hall 4 &ndash; <a href="/resources/maps/hall4-v2.pdf">SEC - Ground floor</a></li>
    <li>Inspiration &ndash; <a href="/resources/maps/village.pdf">The Village</a></li>
    <li>Jura &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - First floor</a></li>
    <li>Keen (Online) &ndash; <a href="/stream">Online (no physical room)</a>
    <li>Lomond Auditorium &ndash; <a href="/resources/maps/sec2.pdf">SEC - Ground floor</a></li>
    <li>Meeting Acadamy M1 &ndash; <a href="/resources/maps/sec2.pdf">SEC - First floor</a></li>
    <li>Meeting Acadamy M2/M3 &ndash; <a href="/resources/maps/sec2.pdf">SEC - First floor</a></li>
    <li>Meeting Acadamy M4 &ndash; <a href="/resources/maps/sec2.pdf">SEC - First floor</a></li>
    <li>Nevis (Online) &ndash; <a href="/stream">Online (no physical room)</a>
    <li>Online 1 &ndash; <a href="/stream">Online (no physical room)</a>
    <li>Online 2 &ndash; <a href="/stream">Online (no physical room)</a>
    <li>Outside Crowne Plaza &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - Outside</a></li>
    <li>Staffa/Shuna &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - First floor</a></li>
    <li>Tours &ndash; <a href="https://glasgow2024.org/whats-on/beyond-programme/tours-and-activities/">Various locations</a></li>
  </ul>
  <h3>Green rooms</h3>
  <ul>
    <li>Boisdale &ndash; <a href="/resources/maps/sec2.pdf">SEC - Ground floor</a></li>
    <li>Mezzanine &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - First floor</a></li>
  </ul>
  <h3>Quiet spaces</h3>
  <ul>
    <li>M5 &ndash; <a href="/resources/maps/sec2.pdf">SEC - First floor</a></li>
    <li>Malin &ndash; <a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza - First Floor</a></li>
  </ul>
  <h3>Safer spaces</h3>
  <ul>
    <li>BIPOC Safer Space &ndash; Room 112 - Crowne Plaza</li>
    <li>LGBTQ+ Safer Space &ndash; Room 115 - Crowne Plaza</li>
</article>
<?php
render_footer();
?>