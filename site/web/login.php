<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/InlineLoginForm.php');
require_once(getenv('CONFIG_LIB_DIR') . '/csrf.php');

csrf_check();

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

$redirect = $_GET['redirect'] ?? '/';

$error = null;
if (isset($_GET['error_code'])) {
    $error = [
        'invalid-state' => 'Your login could not be completed. Please try logging in again.',
        'missing-claims' => 'Your account did not provide the details needed to log in. Please contact support.',
        'unknown-member' => 'No member with that badge number exists.',
        'not-in-allowlist' => 'Sorry, the portal is not open for general access just yet. Watch for an email from the convention announcing it as open.',
    ][$_GET['error_code']] ?? 'An unknown error occurred.';
}

// Each provider's form, or the button that starts its login
$providers = [];
foreach (auth_enabled_providers() as $provider) {
    $providers[] = $provider instanceof InlineLoginForm
        ? ['form' => $provider->inline_form($redirect)]
        : ['form' => null, 'id' => $provider->id(), 'name' => $provider->display_name()];
}

render_page('pages/login.html.twig', [
    'title' => 'Log in',
    'description' => 'Log in to the member portal for ' . CON_NAME . '.',
    'breadcrumbs' => ['Home' => '/', 'Log in'],
    'redirect' => $redirect,
    'error' => $error,
    'providers' => $providers,
    'allow_anonymous' => ALLOW_ANONYMOUS_ACCESS,
    'before_con' => time() < strtotime(CON_START_DATE),
]);
