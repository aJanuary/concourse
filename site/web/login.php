<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/clyde_service.php');

if (is_logged_in()) {
  // NOTE: when we have link to URK+L (RCE etc) and login is needed we should
  // redirect to the requested location rather then the "home" after authentication
  header('Location: /');
  exit;
}

function render_clyde_login_form() {
  $clyde = new ClydeService();

  print '<a href="' . $clyde->authorize_url() . '" class="button">Login with Glasgow Registration</a>';
}

function render_login_form($error=false) {
?>
    <form hx-post="/login" hx-swap="innerHTML" hx-ext="loading-states" data-loading-disable data-loading-aria-busy>
        <?php if ($error) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>
        <fieldset>
            <label for="email">
                <p class="label">Email address</p>
                <p class="hint">The email address you used to register for the convention.</p>
            </label>
            <p><input type="email" id="email" name="email" required autofocus data-loading-disable></p>
        </fieldset>
        <p><input type="submit" value="Get log in link" data-loading-disable></p>
    </form>
<?php
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $reg_status = get_registration_status($email);
    if ($reg_status == "registered") {
        make_session($email);
        header('HX-Redirect: /');
    } else if ($reg_status == "pending") {
        render_login_form('We are still processing your registration. Please try again later.');
    } else if ($reg_status == "blocked") {
        render_login_form('Sorry, we are not open to the general membership just yet. Watch out for an email telling you when the member portal is open.');
    } else {
        render_login_form('No member with this email address can be found. Please check your email address and try again.');
    }
    exit;
}

$error = null;

if (isset($_GET['email']) && isset($_GET['login_code'])) {
    $email = $_GET['email'];
    $login_code = $_GET['login_code'];

    $link_status = get_login_link_status($email, $login_code);
    if ($link_status === "ok") {
        make_session($email);
        header('Location: /');
        exit();
    } else if ($link_status === "expired") {
        $error = 'This log in link has expired. Please request a new log in link.';
    } else {
        $error = 'Invalid login code';
    }
}

if (isset($_GET['error']) && ($_GET['error'] == 'clyde')) {
  // TODO - fill out details of who to contact
  $error = "Sorry, it appears you do not have a membership to access the online convention. Please contact XXXXX";
}

render_header();

?>

<article>
    <h3>Log in</h3>
    <p>If you are having trouble logging in, please e-mail <a href="mailto:<?php echo EMAIL; ?>?subject=Trouble logging in to member portal"><?php echo EMAIL; ?></a>.</p>
    <?php render_login_form($error); ?>
    <p> This will replace the "magic" link ...</p>
    <?php render_clyde_login_form(); ?>
</article>

<?php

render_footer();

?>