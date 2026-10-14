<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/InlineLoginForm.php');

$provider_id = $_GET['provider'] ?? null;
if ($provider_id !== null) {
    $provider = auth_get_provider($provider_id);
    if ($provider === null) {
        render_404();
    }
    $redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? '/';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            finish_login($provider->complete($_POST), $redirect);
        } catch (AuthFailure $e) {
            redirect_to_login_error($e->getMessage(), $redirect);
        }
    }
    $provider->begin($redirect);
}

if (is_logged_in()) {
  header('Location: /');
  exit;
}

if (ALLOW_ANONYMOUS_ACCESS && isset($_GET['skip-login'])) {
    make_anonymous_session();
    $redirect = $_GET['redirect'] ?? '/';
    header('Location: ' . (is_valid_redirect($redirect) ? $redirect : '/'));
    exit();
}

render_header(
    'Log in',
    'Log in to the member portal for ' . CON_NAME . '.',
    ['Home' => '/', 'Log in']
);

?>

<article>
    <h2>Log in</h2>
<?php
if (isset($_GET['error_code'])) {
    $error = [
        'unknown-member' => 'No member with that badge number exists.',
        'not-in-allowlist' => 'Sorry, the portal is not open for general access just yet. Watch for an email from the convention announcing it as open.',
    ][$_GET['error_code']] ?? 'An unknown error occurred.';
?>
    <p class="error"><img src="resources/error.svg" alt="error" aria-label="error"><?php echo $error; ?></p>
<?php
}
foreach (auth_enabled_providers() as $provider) {
    if ($provider instanceof InlineLoginForm) {
        $provider->render_inline_form($_GET['redirect'] ?? '/');
        continue;
    }
?>
    <a href="?provider=<?php echo urlencode($provider->id()); ?>&redirect=<?php echo urlencode($_GET['redirect'] ?? '/'); ?>" class="button login"><?php echo htmlspecialchars($provider->display_name()); ?></a>
<?php
}
if (ALLOW_ANONYMOUS_ACCESS) {
?>
    <p><a href="/login?skip-login&redirect=<?php echo urlencode($_GET['redirect'] ?? '/'); ?>">Continue without logging in</a></p>
<?php
}
?>
    <p>Logging in to the member portal allows us to customise your online experience and give you quick access to the things most relevant to you. We also require you to log in to access streaming programme via RingCentral Events and chatting via the Discord.</p>
<?php
    if (time() < strtotime(CON_START_DATE)) {
?>
    <h3>Not a member?</h3>
    <p>If you&apos;re not yet a member of the convention, you can still <a href="https://example.com/buy-membership">purchase a membership or ticket</a>.</p>
<?php
    }
?>
    <h3>Who can log in the portal?</h3>
    <p>People aged 16 and over who have Attending Memberships, Day Tickets, Online Memberships, or Online Tickets can log in the portal.
    <?php
    if (time() < strtotime(CON_START_DATE)) {
    ?>
        If you don&apos;t already have one of these and would like to access the portal, you can <a href="https://example.com/buy-membership">purchase a membership or ticket</a>.
    <?php
    }
    ?>
    <?php
if (ALLOW_ANONYMOUS_ACCESS) {
?>
    <p>If you do not wish to log in, or cannot (for example, because you are under 16), then you can click the "Continue without logging in" link to access most of the portal.</p>
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