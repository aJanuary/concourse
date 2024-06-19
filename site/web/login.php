<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/clyde_service.php');

if (is_logged_in()) {
  header('Location: /');
  exit;
}

$error = null;

if (isset($_GET['skip-login'])) {
    make_anonymous_session();
    header('Location: ' . $_GET['redirect']);
    exit();
}

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

$clyde = new ClydeService();
$_SESSION['oauth2redirect'] = $_GET['redirect'] ?? '/';

render_header();

?>

<article>
    <h3>Log in</h3>
<?php
if (isset($_GET['error_code'])) {
    $error = [
        'invalid-state' => 'An error occured while logging in with Glasgow Registration. Please try again.',
        'no-code' => 'Unable to log in with Glasgow Registration. Please try again and make sure you click the "Authorize" button after reviewing the permissions.',
        'no-access' => 'Your membership does not include access to the online convention. If you think this is a mistake, please e-mail <a href="mailto:registration@glasgow2024.org">registration@glasgow2024.org</a>.',
        'under-age' => 'Your membership indicates you are under 16 and not allowed to access the online convention. If you think this is a mistake, please e-mail <a href="mailto:registration@glasgow2024.org">registration@glasgow2024.org</a>.<p>You can still access most of the portal by clicking "Continue without logging in", but you will not be able to access any content that is age restricted such as watching streams or joining the Discord.</p>',        'apocraphyl' => 'Your membership indcates that you are nt human! If you think this is a mistake, please e-mail <a href="mailto:registration@glasgow2024.org">registration@glasgow2024.org</a>.',
        'duplicate-email' => 'An account with this e-mail address already exists. Please log in with your existing account.',
        'not-in-allowlist' => 'Sorry, the portal is not open for general access just yet. Watch for an email from the convention announcing it as open.',
    ][$_GET['error_code']] ?? 'An unknown error occured.';
?>
    <p class="error"><?php echo $error; ?></p>
<?php
}
?>
    <p>If you are having trouble logging in, please e-mail <a href="mailto:<?php echo SUPPORT_EMAIL; ?>?subject=Trouble logging in to member portal"><?php echo SUPPORT_EMAIL; ?></a>.</p>
    <a href="<?php echo $clyde->authorize_url(); ?>" class="button">Login with Glasgow Registration</a>
    <p><a href="/login?skip-login&redirect=<?php echo urlencode($_SESSION['oauth2redirect']); ?>">Continue without logging in</a></p>
</article>

<?php

render_footer();

?>