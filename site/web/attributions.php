<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Icon attributions',
  'Attributions for the icons used on the site.',
  ['Home' => '/', 'Icon attributions']
);
?>
<article>
  <h2>Icon attributions</h2>
  <ul>
    <li>Alarm clock icon by <a href="https://lorcblog.blogspot.com/">Lorc</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Archive research icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Astrolabe icon by <a href="https://game-icons.net/">Caro Asercion</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Book pile icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Classical knowledge icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Cloud upload icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Cog icon by <a href="https://lorcblog.blogspot.com/">Lorc</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Direction signs icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Help icon by <a href="http://opengameart.org/content/95-game-icons">sbed</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Info icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Newspaper icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Notebook book icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Palm icon by <a href="https://lorcblog.blogspot.com/">Lorc</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Party popper icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Pencil brush upload icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Photo camera icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a></li>
    <li>Play button icon by <a href="https://guard13007.com/">Guard13007</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Present icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a></li>
    <li>Portal icon by <a href="https://lorcblog.blogspot.com/">Lorc</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Position upload icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Puzzle icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Spell book icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Truce icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a></li>
    <li>Sunflower icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
    <li>Watch icon by <a href="https://delapouite.com/">Delapouite</a> under <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a></li>
  </ul>
</article>
<?php
render_footer();
?>