<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header("Instructions on how to log in to the Glasgow 2024 member portal.");
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h2>How to log in</h2>
  <ol class="instructions">
      <li>
        <p>Click the "Log in with Glasgow Registration".</p>
        <p><img src="/resources/login/login-button.webp" alt="A screenshot of a blue button that says 'Log in with Glasgow Registration'" width="640" height="220"/></p>
      </li>
      <li>
          <p>Log in to the Glasgow 2024 Registration system.</p>
          <p><em>Note: If you have already logged in to the registration system, you will automatically be taken to step 3.</em></p>
          <p>You can either log using Email Log In (the default), or Direct Log In.
          <h3>Email Log In</h3>
          <ol>
              <li>Enter the email address you used to register for the convention.</li>
              <li>Enter your ticket number. You will be able to find this on your badge and in the e-mail that announced the opening of the portal.</li>
              <li>Click "Email Login".</li>
              <li>Check your email for a message from Glasgow 2024 Registration. This email will contain a link that you can use to log in to the portal.</li>
              <li>Click on the "Login" button in the email.</li>
          </ol>
          <p><img src="/resources/login/email-login.webp" alt="A screenshot of the email log in screen" width="652" height="364" /></p>
          <h3>Direct Log In</h3>
          <ol>
              <li>
                <p>Click the "Direct Login" link.</p>
                <p><img src="/resources/login/direct-login-link.webp" alt="A screenshot of the email log in screen with the 'Direct login' link highlighted" width="652" height="364" /></p>
              </li>
              <li>Enter the email address you used to register for the convention.</li>
              <li>Enter your ticket number. You will be able to find this on your badge and in the e-mail that announced the opening of the portal.</li>
              <li>Enter the password you set when creating your account.</li>
              <li>Click the "Direct Login" button.</li>
          </ol>
          <p><img src="/resources/login/direct-login.webp" alt="A screenshot of the direct log in screen" width="659" height="565" /></p>
      </li>
      <li>
        <p>Click the "Authorize" button to allow the portal to access your registration information.</p>
        <p><img src="/resources/login/authorize.webp" alt="A screenshot of the authorize screen" width="354" height="389" /></p>
      </li>
  </ol>
</article>
<?php
render_footer();
?>