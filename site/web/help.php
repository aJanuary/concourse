<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Help',
  'How to get help at ' . CON_NAME . '.',
  ['Home' => '/', 'Help']
);
?>
<article class="instructions">
  <h2>Help</h2>
  
  <p>There are many ways to get help, using whatever route is most convenient for you.</p>

  <h3><a href="#online" name="online">Online</a></h3>
  <ul>
    <li>In the #help-desk channel of the <a href="/chat">Discord server</a></li>
    <li>E-mail <a href="mailto:info@eastercon2026.org">info@eastercon2026.org</a></li>
  </ul>

  <h3><a href="#in-person" name="in-person">In person</a></h3>
  <ul>
    <li>Go to Ops located in the <a href="https://map.eastercon2026.org/room/oxford">Oxford room</a></li>
    <li>Find the on-duty committee member. They will be wearing a hi-viz vest</li>
    <li>Talk to the hotel staff</li>
  </ul>
</article>
<?php
render_footer();
?>