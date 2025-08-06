<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Virtual gatherings',
  'Virtual gatherings for ' . CON_NAME . '.',
  ['Home' => '/', 'Virtual gatherings']
);
?>
<article>
  <h2>Virtual gatherings</h2>
  <p>Virtual gatherings are a space for you and or your group to host a hangout within <a href="/stream">RingCentral Events</a>. This could be anything from a writers club wanting to meet, to a D&amp;D session being held. The sky &ndash; and the <a href="https://example.com/code-of-conduct/">code of conduct</a> &ndash; are the limits of what can be hosted in these conference style AV rooms.</p>
  <p>To join a gathering, go to <a href="/stream">RingCentral Events</a>, select "Sessions" from the sidebar, and then click on the session for the gathering you want to join.</p>
  
  <h3>Requesting a gathering</h3>
  <p>If you want to host your own gathering, please fill out the form. We will create a session in RingCentral Events and notify you via e-mail when it is set up.</p>
  <p>If you would like, you can advertise the gathering in the #meeting-people channel of the <a href="/chat">Discord</a>.

  <?php
      if (is_anonymous()) {
  ?>
        <p><a class="button" href="<?php echo make_login_link(); ?>">Log in to request a virtual gathering</a></p>
  <?php
      } else {
        $url = "https://example.com/request-virtual-gathering?username=" . urlencode($_REQUEST["username"]) . "&email=" . urlencode(get_current_user_email());
  ?>
    <p><a class="button" target="_blank" href="<?php echo $url; ?>">Request a virtual gathering</a></p>
  <?php
      }
  ?>
</article>
<?php
render_footer();
?>