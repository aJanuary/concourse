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
<article class="instructions">
  <h2>Help</h2>
  <p>There are many ways to get help, using whatever route is most convenient for you.</p>
  <ul>
    <li><a href="#faq">FAQ</a></li>
    <li><a href="#reporting">Reporting potential Code of Conduct violations</a></li>
    <li><a href="#discord">Discord</a></li>
    <li><a href="#rce">RingCentral Events</a></li>
    <li><a href="#in-person">In person</a></li>
    <li><a href="#email">E-mail</a></li>
    <li><a href="#telephone">Telephone</a></li>
  </ul>

  <h3><a name="faq">FAQ</a></h3>
  <p>We have assembled the answers to some frequently asked questions on the <a href="/under-construction?page=faq">FAQ</a> page.</p>

  <h3><a name="reporting">Reporting potential Code of Conduct violations</a></h3>
  <p>The <a href="https://glasgow2024.org/about/policies/code-of-conduct/cocv-reporting-methods/">methods of reporting</a> page outlines the many routes available to report a potential Code of Conduct violation.</p>

  <h3><a name="discord">Discord</a></h3>
  <p>After <a href="/chat">joining our Discord server</a>, you can ask for help in the #help-desk channel.</p>
  <p>This can be a good option if you want to have a text conversation with someone.</p>
  <p><em>TODO: Screenshot</em></p>

  <h3><a name="rce">RingCentral Events</a></h3>
  <p>After <a href="/stream">joining our RingCentral Events event</a>, you can ask for help by going to the Help Desk tab on the left/bottom and then clicking Help Desk.</p>
  <p>This can be a good option if you want to would like to have a video chat with someone.</p>
  <p><img src="/resources/help/rce.png" alt="A screenshot of RingCentral Events"></p>

  <h3><a name="in-person">In person</a></h3>
  <h4>Information desk</h4>
  <p>For general information and questions.</p>
  <p>Located in Hall 4 of the SEC near the 4A entrance.</p>
  <p><a href="/times#information-desk">Open hours</a></p>
  <p><img src="/resources/help/info.png" alt="Map of Hall 4 with the Information Desk circled"></p>

  <h4>Convention operations</h4>
  <p>General help and assistance, lost property and out-of-hours cover.</p>
  <p>Located in the concourse of the SEC and Rockhall in the Crowne Plaza.</p>
  <p><a href="/times#con-ops">Open hours</a></p>
  <p><em>TODO: Insert a map here</em></p>

  <h4>Accessibility desk</h4>
  <p>For help with accommodating accessibility needs.</p>
  <p>Located in Hall 4 of the SEC near the 4A entrance.</p>
  <p><a href="/times#accessibility-desk">Open hours</a></p>
  <p><img src="/resources/help/accessibility.png" alt="Map of Hall 4 with the Accessibility Desk circled"></p>

  <h4>Listeners desk</h4>
  <p>Talk to someone privately about a sensitive topic, including potential Code of Conduct violations.</p>
  <p>Located in Hall 4 of the SEC near the 4A entrance.</p>
  <p><a href="/times#listeners-desk">Open hours</a></p>
  <p><img src="/resources/help/listeners.png" alt="Map of Hall 4 with the Accessibility Desk circled"></p>

  <h4>Registration desk</h4>
  <p>For registration questions and problems.</p>
  <p>Located in the concourse of the SEC.</p>
  <p><em>TODO: Insert a map here</em></p>

  <h3><a name="e-mail">E-mail</a></h3>
  <p><a href="mailto:info@glasgow2024.org">info@glasgow2024.org</a> &mdash; General information and questions</p>
  <p><a href="mailto:online-convention-help@glasgow2024.org">online-convention-help@glasgow2024.org</a> &mdash; Get help with this member portal, Discord, or RingCentral Events</p>
  <p><a href="mailto:registration@glasgow2024.org">registration@glasgow2024.org</a> &mdash; Registration questions and problems</p>
  <p><a href="mailto:accessibility@glasgow2024.org">accessibility@glasgow2024.org</a> &mdash; Accessibility Services</p>
  <p><a href="mailto:coc@glasgow2024.org">coc@glasgow2024.org</a> &mdash; To report a potential <a href="https://glasgow2024.org/about/policies/code-of-conduct/">Code of Conduct</a> violation.</p>
  <p>You can find other, more specialised e-mail addresses on the <a href="https://glasgow2024.org/about/contact/">Contact</a> page.</p>

  <h3><a name="telephone">Telephone</a></h3>
  <p>There is a telephone number on the back of your badge that you can call for help.</p>
</article>
<?php
render_footer();
?>