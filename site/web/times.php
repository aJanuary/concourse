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

  <h3><a href="#registration" name="registration">Registration desk</a></h3>
  <p>Located in the <a href="https://map.eastercon2026.org/room/registration">main lobby</a>. If you need to pick up your badge out-of-hours, you can do so from the <a href="https://map.eastercon2026.org/room/oxford">Ops Hub in Oxford</a>.</p>
  <dl class="times-list">
    <div>
      <dt>Thursday, 2 April</dt>
      <dd>16:00&ndash;18:00</dd>
    </div>
    <div>
      <dt>Friday, 3 April</dt>
      <dd>09:00&ndash;17:00</dd>
    </div>
    <div>
      <dt>Saturday, 4 April</dt>
      <dd>09:00&ndash;17:00</dd>
    </div>
    <div>
      <dt>Sunday, 5 April</dt>
      <dd>09:00&ndash;17:00</dd>
    </div>
  </dl>

  <h3><a href="#artshow" name="artshow">Art show</a></h3>
  <p>Located in <a href="https://map.eastercon2026/room/princes">Princes</a></p>
  <dl class="times-list">
    <div>
      <dt>Friday, 3 April</dt>
      <dd>15:00&ndash;17:00 (previews)</dd>
      <dd>17:00&ndash;20:00</dd>
    </div>
    <div>
      <dt>Saturday, 4 April</dt>
      <dd>10:00&ndash;18:00</dd>
    </div>
    <div>
      <dt>Sunday, 5 April</dt>
      <dd>10:00&ndash;16:00</dd>
      <dd>16:00&ndash;18:00 (collection of purchased work)</dd>
    </div>
  </dl>

  <h3><a href="#games" name="games">Games room</a></h3>
  <p>Located in <a href="https://map.eastercon2026/room/london">London</a> with overflow in the <a href="https://map.eastercon2026/room/stairs-upper-floor">upper floor rooms</a> (Note: the upper floor rooms do not have step-free access)</p>
  <dl class="times-list">
    <div>
      <dt>Friday, 3 April</dt>
      <dd>15:00&ndash;20:00</dd>
    </div>
    <div>
      <dt>Saturday, 4 April</dt>
      <dd>10:00&ndash;20:00</dd>
    </div>
    <div>
      <dt>Sunday, 5 April</dt>
      <dd>10:00&ndash;20:00</dd>
    </div>
  </dl>
</article>
<?php
render_footer();
?>