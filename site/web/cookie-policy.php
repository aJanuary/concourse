<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Cookie policy',
  'An explanation of the cookies we set and what they are used for',
  ['Home' => '/', 'Cookie policy']
);
?>
<article>
  <h2>Cookie policy</h2>
  <em>This Cookie Policy was last updated on 23<sup>rd</sup> June 2024 and applies to citizens and legal permanent residents of the European Economic Area and Switzerland.</em>
  <h3>1. Introduction</h3>
  <p>Our website, <?php echo $ROOT_URL; ?> (hereinafter: "the website") uses cookies and other related technologies (for convenience all technologies are referred to as "cookies"). In the document below we inform you about the use of cookies on our website.</p>
  <h3>2. What are cookies?</h3>
  <p>A cookie is a small simple file that is sent along with pages of this website and stored by your browser on the hard drive of your computer or another device. The information stored therein may be returned to our servers during a subsequent visit.</p>
  <h3>3. What are scripts?</h3>
  <p>A script is a piece of program code that is used to make our website function properly and interactively. This code is executed on our server or on your device.</p>
  <h3>4. Cookies</h3>
  <h4>4.1 Technical or functional cookies</h4>
  <p>Some cookies ensure that certain parts of the website work properly and that your user preferences remain known. By placing functional cookies, we make it easier for you to visit our website. This way, you do not need to repeatedly enter the same information when visiting our website and, for example, the items remain in your shopping cart until you have paid. We may place these cookies without your consent.</p>
  <h3>5. Placed cookies</h3>
  <p>We set the following cookies to provide the functionality of the website. This data is not shared with third parties.</p>
  <ul>
    <li>PHPSESSID - Stores the user&apos;s temporary log in session id. Required for log in functionality to work. Expires when the browser session ends.</li>
    <li>session - Stores the user&apos;s session id. Required for log in functionality to work. Expires after 30 days.</li>
    <li>seen_rce_invite - Stores whether the user has seen the RingCentral Events invite. Required for deep-linking into RingCentral Event items. Expires after 30 days.</li>
  </ul>
  <h3>6. Consent</h3>
  <p>Because we only make use of cookies to provide essential functionality, we do not require your consent to set these cookies. You can still block or delete them by changing your browser settings.</p>
  <h3>7. Enabling/disabling and deleting cookies</h3>
  <p>You can use your internet browser to automatically or manually delete cookies. You can also specify that certain cookies may not be placed. Another option is to change the settings of your internet browser so that you receive a message each time a cookie is placed. For more information about these options, please refer to the instructions in the Help section of your browser.</p>
  <p>Please note that our website may not work properly if all cookies are disabled. If you do delete the cookies in your browser, they will be placed again after your consent when you visit our website again.</p>
  <h3>8. Your rights with respect to personal data</h3>
  <p>You have the following rights with respect to your personal data:</p>
  <ul>
    <li>You have the right to know why your personal data is needed, what will happen to it, and how long it will be retained for.</li>
    <li>Right of access: You have the right to access your personal data that is known to us.</li>
    <li>Right to rectification: you have the right to supplement, correct, have deleted or blocked your personal data whenever you wish.</li>
    <li>If you give us your consent to process your data, you have the right to revoke that consent and to have your personal data deleted.</li>
    <li>Right to transfer your data: you have the right to request all your personal data from the controller and transfer it in its entirety to another controller.</li>
    <li>Right to object: you may object to the processing of your data. We comply with this, unless there are justified grounds for processing.</li>
  </ul>
  <p>To exercise these rights, please contact us. Please refer to the contact details at the bottom of this Cookie Policy. If you have a complaint about how we handle your data, we would like to hear from you, but you also have the right to submit a complaint to the supervisory authority (the Data Protection Authority).</p>
  <h3>9. Contact details</h3>
  <p>For questions and/or comments about our Cookie Policy and this statement, please contact us by using the following contact details:</p>
  <p>Glasgow 2024 - A Worldcon for Our Futures<br>
  The Scottish Event Campus (SEC) and the connected Crowne Plaza Hotel<br>
  Glasgow<br>
  United Kingdom<br>
  Website: <a href="https://glasgow2024.org">https://glasgow2024.org</a><br>
  Email: <a href="mailto:info@glasgow2024.org">info@glasgow2024.org</a></p>
</article>
<?php
render_footer();
?>