<?php
$_REQUEST['allow_implicit_anonymous'] = 'always';
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Log in instructions',
  'Instructions on how to log in to the Glasgow 2024 member portal.',
  ['Home' => '/', 'How to log in']
);
?>
<article>
  <h2>How to log in</h2>
  <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/UokaIYwF2LE?si=UwK7e8PjSRNVS8wM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
  <ol class="instructions">
      <li>
        <p>Click the "Log in with Glasgow Registration".</p>
        <p><img src="/resources/login/login-button.webp" alt="A screenshot of a blue button that says 'Log in with Glasgow Registration'" width="640" height="220"/></p>
      </li>
      <li>
          <p>Log in to the Glasgow 2024 Registration system.</p>
          <p><em>Note: If you have already logged in to the registration system, you will automatically be taken to step 3.</em></p>
          <ol>
              <li>Enter the email address you used to register for the convention.</li>
              <li>Enter your ticket number. You will be able to find this on your badge and in the e-mail that announced the opening of the portal. Note, this will be the number without the # sign i.e. 0933, not #0933.</li>
              <li>Click "Email Login".</li>
              <li>Check your email for a message from Glasgow 2024 Registration. This email will contain a link that you can use to log in to the portal.</li>
              <li>
                <p>Click on the "Login" button in the email.</p>
                <p><img src="/resources/login/email-login.webp" alt="A screenshot of the email log in screen" width="652" height="364" /></p>
              </li>
              <li>
                <p>If you see this screen, unfortunately this means the log in has not worked correctly. See <a href="#registration-dashboard">What to do if you get to the Registration dashboard</a>.</p>
                <p><img src="/resources/login/registration-dashboard.webp" alt="A screenshot of a screen that says 'Registration dashboard' in the top left" width="480" height="270" /></p>
              </li>
          </ol>
      </li>
      <li>
        <p>Click the "Authorize" button to allow the portal to access your registration information.</p>
        <p><img src="/resources/login/authorize.webp" alt="A screenshot of the authorize screen" width="354" height="389" /></p>
      </li>
      <li>
        <p>If you see this screen, see <a href="#another-castle">What to do if you get the "Your Princess is in another castle" screen</a>.</p>
        <p><img src="/resources/login/another-castle.webp" alt="A screenshot of a screen that says 'Your Princess is in another castle'" width="480" height="270" /></p>
      </li>
      <li>
        <p>You should now see the Glasgow 2024 member portal.</p>
        <p><img src="/resources/login/member-portal.webp" alt="A screenshot of the member portal" width="640" height="360" /></p>
      </li>
  </ol>

  <h3><a href="#registration-dashboard" id="registration-dashboard">What to do if you get to the Registration dashboard</a></h3>
  <ol>
    <li>Click your name in the top right</li>
    <li>Click "Log out"</li>
    <li>Close the tab, and any other tabs relating to the Registration dashboard and Member portal</li>
    <li>Start the log in process again at <a href="https://portal.glasgow2024.org">https://portal.glasgow2024.org</a></li>
  </ol>
  <p><img src="/resources/login/registration-dashboard-steps.webp" alt="A screenshot of a screen that says 'Registration dashboard' in the top left" width="1920" height="1080" /></p>

  <h3><a href="#another-castle" id="another-castle">What to do if you get the "Your Princess is in another castle" screen</a></h3>
  <p>This typically happens because you started the log in process in one browser, but the log in button in the e-mail opened in another.</p>
  <p>Note, if you are trying to log in with mutliple browsers, you must start the log in proces from the beginning on each device by going to <a href="https://portal.glasgow2024.org">https://portal.glasgow2024.org</a>.</p>
  <ol>
    <li>
      <p>Click the "Copy" button to copy the URL to your clipboard</p>
      <p><img src="/resources/login/another-castle-step1.webp" alt="A screenshot of a screen that says 'Your Princess is in another castle' with an '1' annotation next to the copy button" width="1920" height="1080" /></p>
    </li>
    <li>Go to the browser you started the log in process with</li>
    <li>
      <p>Paste the URL into the address bar</p>
      <p><img src="/resources/login/paste-url.webp" alt="A screenshot of a browser with a '3' annotation next to the URL bar" width="1920" height="1080" /></p>
    </li>
  </ol>
</article>
<?php
render_footer();
?>