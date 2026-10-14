<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Opening times',
  'Opening times for ' . CON_NAME . '.',
  ['Home' => '/', 'Opening times']
);
?>
<article>
  <h2>Opening times</h2>
  <p>The opening times will be updated as we get closer to the event. Please check back regularly for the latest information.</p>
<?php
// Here is an example of how to structure the opening times.
/*
  <ul id="times-toc">
    <li><a href="#accessibility-desk">Accessibility desk</a></li>
    <li><a href="#con-ops">Con operations</a></li>
    <li><a href="#information-desk">Information desk</a></li>
    <li><a href="#listeners-desk">Listeners desk</a></li>
  </ul>

  <h3><a href="#accessibility-desk" name="accessibility-desk">Accessibility desk</a></h3>
  <ul class="times-list">
    <li>
      <p>Friday, 1 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>Main hall</p>
    </li>
    <li>
      <p>Saturday, 2 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>Main hall</p>
    </li>
    <li>
      <p>Sunday, 3 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>Main hall</p>
    </li>
  </ul>

  <h3><a href="#con-ops" name="con-ops">Con operations</a></h3>
  <ul class="times-list">
    <li>
      <p>Friday, 1 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>Next to registration</p>
    </li>
    <li>
      <p>Saturday, 2 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>Next to registration</p>
    </li>
    <li>
      <p>Sunday, 3 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>Next to registration</p>
    </li>
  </ul>

  <h3><a href="#information-desk" name="information-desk">Information desk</a></h3>
  <ul class="times-list">
    <li>
      <p>Friday, 1 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>Main hall</p>
    </li>
    <li>
      <p>Saturday, 2 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>Main hall</p>
    </li>
    <li>
      <p>Sunday, 3 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>Main hall</p>
    </li>
  </ul>

  <h3><a href="#listeners-desk" name="listeners-desk">Listeners desk</a></h3>
  <ul class="times-list">
    <li>
      <p>Friday, 1 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>Main hall</p>
    </li>
    <li>
      <p>Saturday, 2 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>Main hall</p>
    </li>
    <li>
      <p>Sunday, 3 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>Main hall</p>
    </li>
  </ul>
*/ ?>
</article>
<?php
render_footer();
?>