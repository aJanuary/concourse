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

if (isset($_GET['badge_no']) && isset($_GET['login_code'])) {
    $badge_no = $_GET['badge_no'];
    $login_code = $_GET['login_code'];

    $link_status = get_login_link_status($badge_no, $login_code);
    if ($link_status === "ok") {
        make_session($badge_no);
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

render_header(
    'Log in',
    'Log in to the member portal for Glasgow 2024, A Worldcon for Our Futures.',
    ['Home' => '/', 'Log in']
);

?>

<article>
    <h2>Log in</h2>
<?php
if (isset($_GET['error_code'])) {
    $error = [
        'invalid-state' => 'An error occured while logging in with Glasgow Registration. Please try again.',
        'no-code' => 'Unable to log in with Glasgow Registration. Please try again and make sure you click the "Authorize" button after reviewing the permissions.',
        'no-access' => 'Your membership does not include access to the online convention. If you think this is a mistake, please e-mail <a href="mailto:registration@glasgow2024.org">registration@glasgow2024.org</a>.',
        'under-age' => 'Your membership indicates you are under 16 and not allowed to access the online convention. If you think this is a mistake, please e-mail <a href="mailto:registration@glasgow2024.org">registration@glasgow2024.org</a>.<p>You can still access most of the portal by clicking "Continue without logging in", but you will not be able to access any content that is age restricted such as watching streams or joining the Discord.</p>',
        'duplicate-email' => 'An account with this e-mail address already exists. Please log in with your existing account.',
        'not-in-allowlist' => 'Sorry, the portal is not open for general access just yet. Watch for an email from the convention announcing it as open.',
    ][$_GET['error_code']] ?? 'An unknown error occured.';
?>
    <p class="error"><img src="resources/error.svg" alt="error" aria-label="error"><?php echo $error; ?></p>
<?php
}
?>
    <a href="<?php echo $clyde->authorize_url(); ?>" class="button login">Log in with Glasgow Registration</a>
    <p><a href="/login?skip-login&redirect=<?php echo urlencode($_SESSION['oauth2redirect']); ?>">Continue without logging in</a></p>
    <p>Logging in to the members portal allows us to customise the experience to your membership and give you quick access to the things most relevant to you. We also require you to log in to access streaming programme via RingCentral Events and chatting via the Discord.</p>
    <p>If you do not wish to log in, or cannot (for example, because you are under 16), then you can click the "Continue without logging in" link to access most of the portal.
<?php
    if (time() < strtotime("2024-08-13")) {
?>
    <h3>Not a member?</h3>
    <p>If you&apos;re not yet a member of the convention, you can still <a href="https://glasgow2024.org/for-members/memberships-and-tickets/">become a member</a>.</p>
<?php
    }
?>
    <h3>Problems</h3>
    <p>Read the <a href="/login-instructions" target="_blank">instructions on how to log in</a>.</p>
    <p>If you are having trouble logging in, please e-mail <a href="mailto:<?php echo SUPPORT_EMAIL; ?>?subject=Trouble logging in to member portal"><?php echo SUPPORT_EMAIL; ?></a>.</p>
</article>

<?php

render_footer();

?>