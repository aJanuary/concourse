<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Help',
  'How to get help at Glasgow 2024',
  ['Home' => '/', 'Help']
);
?>
<article>
  <h2>Help</h2>
  <p>There are many ways to get help, using whatever route is most convenient for you.</p>

  <h3>FAQ</h3>
  <p>We have assembled the answers to some frequently asked questions on the <a href="/under-construction?page=faq">FAQ</a> page.</p>

  <h3>E-mail</h3>
  <p><a href="mailto:info@glasgow2024.org">info@glasgow2024.org</a> &mdash; General information and questions</p>
  <p><a href="mailto:online-support@glasgow2024.org">online-support@glasgow2024.org</a> &mdash; Get help with this member portal, Discord, or RingCentral Events</p>
  <p><a href="mailto:registration@glasgow2024.org">registration@glasgow2024.org</a> &mdash; Registration questions and problems</p>
  <p><a href="mailto:accessibility@glasgow2024.org">accessibility@glasgow2024.org</a> &mdash; Accessibility Services</p>
  <p><a href="mailto:coc@glasgow2024.org">coc@glasgow2024.org</a> &mdash; To report a potential <a href="https://glasgow2024.org/about/policies/code-of-conduct/">Code of Conduct</a> violation.</p>
  <p>You can find other, more specialised e-mail addresses on the <a href="https://glasgow2024.org/about/contact/">Contact</a> page.</p>

  <h3>Discord</h3>
  <p>After <a href="/chat">joining our Discord server</a>, you can ask for help in the #help-desk channel.</p>
  <p><em>TODO: Screenshot</em></p>
  <p>This can be a good option if you want to have a text conversation with someone.</p>

  <h3>RingCentral Events</h3>
  <p>After <a href="/stream">joining our RingCentral Events event</a>, you can ask for help by going to the Offices tab on the left and clicking Help Desk.</p>
  <p><em>TODO: Screenshot</em></p>
  <p>This can be a good option if you want to would like to have a video chat with someone.</p>

  <h3>In person</h3>
  <h4>Information Desk</h4>
  <p>For general information and questions.</p>
  <p>Located in the Exhibit hall near the <em>TODO</em> entrance.</p>
  <p><em>TODO: Insert a map here</em></p>

  <h4>Online Help Desk</h4>
  <p>For in-person help with this member portal, Discord, or RingCentral Events.</p>
  <p>Located in the Exhibit hall near the <em>TODO</em> entrance.</p>
  <p><em>TODO: Insert a map here</em></p>

  <h4>Registration Desk</h4>
  <p>For registration questions and problems.</p>
  <p>Located <em>TODO</em>.</p>
  <p><em>TODO: Insert a map here</em></p>

  <h4>Accessibility Desk</h4>
  <p>For help with accomodating accessibility needs.</p>
  <p>Located <em>TODO</em>.</p>
  <p><em>TODO: Insert a map here</em></p>
</article>
<?php
render_footer();
?>