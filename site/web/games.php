<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = 'always';
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-games');

render_header(
  'Games',
  'Games at ' . CON_NAME . '.',
  ['Home' => '/', 'Games']
);
?>
<article>
  <h2>Games</h2>

  <h3>Games room</h3>

  <p>The Games Room will be located in <a href="https://map.eastercon2026.org/room/london">London</a> and contains board games and tables for play. This is also where the RPGs listed on the schedule will be played! You’re welcome to hang out in the Games Room when people aren’t playing, but if people are looking for a space to play a game, please make way for them and move into other areas of the con.
  Overflow gaming space is available in the bar or in <a href="https://map.eastercon2026.org/room/stairs-mezzanine">Ascot and Sunningdale</a>, but note that the latter rooms are only accessible by going up some stairs.</p>
  <p>We are looking for volunteers to look after the Games Room during the convention; if you are interested, please visit <a href="https://map.eastercon2026.org/room/oxford">Ops in Oxford</a> or talk to John Coxon in the Games Room.</p>
  <p>The opening times are as follows:</p>
  <dl class="times-list">
    <div>
      <dt>Friday, 3 April</dt>
      <dd>15:00&ndash;21:00</dd>
    </div>
    <div>
      <dt>Saturday, 4 April</dt>
      <dd>10:00&ndash;21:00</dd>
    </div>
    <div>
      <dt>Sunday, 5 April</dt>
      <dd>10:00&ndash;21:00</dd>
    </div>
    <div>
      <dt>Monday, 6 April</dt>
      <dd>10:00&ndash;14:00</dd>
    </div>
  </dl>

  <h3>Game sign-ups</h3>
  <p>We are running some games which are listed on <a href="https://guide.eastercon2026.org">ConClár</a>. If you are interested in signing up for these, fill in one of the forms on the <a href="/sign-ups">Sign-ups page</a>. Content warnings and age ranges for each game are available on the programme or in the sign-up form. Pre-generated characters, dice and pencils will be available for all roleplaying games.</p>
</article>

<?php
render_footer();
?>
