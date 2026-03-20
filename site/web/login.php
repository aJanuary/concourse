<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/conreg_db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/oauth_service.php');
require_once(getenv('CONFIG_LIB_DIR') . '/mail.php');

// These phases are protected by the token in the URL.
// The user should not be able to directly set them.
$protected_phases = ['select-user'];

$error = null;

$phase = $_POST['phase'] ?? $_GET['phase'] ?? null;

if (in_array($phase, $protected_phases)) {
    $phase = null;
}

$email = trim($_POST['email'] ?? $_GET['email'] ?? null);
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? '/';
// Some URLs will just cause confusion when we redirect back to them.
// For those, swap it out for / instead.
if (!is_valid_redirect($redirect)
    || str_starts_with($redirect, "/forgot-password")
    || str_starts_with($redirect, "/login")) {
    $redirect = '/';
}

if (!$phase && $_GET['token']) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $token_data = verify_secure_link($path, $_GET['token']);
    if ($token_data === 'expired') {
        $error = 'Your login has expired. Please try logging in again.';
    } else if ($token_data === 'invalid') {
        $error = 'Invalid login link. Please try logging in again.';
    } else {
        $phase = $token_data['phase'];
        $email = trim($token_data['email'] ?? null);
        $redirect = $token_data['redirect'] ?? '/';
    }
}

if ($phase === 'entered-email') {
    if ($email === "") {
        $error = "You must enter an email";
    } else if (db_email_has_password_auth($email)) {
        $phase = 'enter-password';
    } else {
        header('Location: /login?phase=requested-login-code&redirect=' . urlencode($redirect) . '&email=' . urlencode($email));
        exit();
    }
} else if ($phase === 'entered-password') {
    $password = $_POST['password'];
    if (db_check_password_auth($email, $password)) {
        $token = create_secure_link('/login', [
            'phase' => 'select-user',
            'email' => $email,
            'redirect' => $redirect,
        ], 300);
        header('Location: ' . $token);
        exit();
    } else {
        header('Location: /login?phase=entered-email&redirect=' . urlencode($redirect) . '&email=' . urlencode($email) . '&error_code=incorrect-password');
        exit();
    }
} else if ($phase === 'select-user') {
    $members = conreg_get_members_from_email($email);
    error_log("Members for $email: " . print_r($members, true));
    if (count($members) == 1) {
        $badge_no = $members[0]['badge_no'];
        logout();
        make_session($badge_no);
        header('Location: ' . ($redirect ?? '/'));
        exit();
    } else if (count($members) == 0) {
        // Could direct them to registration, but to get here they must have bypassed the first step where
        // they enter an email, so just do the simple thing and show an error.
        $error = 'No member found with this e-mail address.';
    }
} else if ($phase === 'selected-user') {
    make_session($token_data['badge_no']);
    header('Location: ' . $token_data['redirect']);
    exit();
} else if ($phase === 'requested-login-code') {
    $result = send_login_code($email, $redirect);
    if ($result !== 'ok') {
        $error = '&error_code=' . urlencode($result);
    } else {
        $error = '';
    }
    header('Location: /login?phase=enter-code&redirect=' . urlencode($redirect) . '&email=' . urlencode($email) . $error);
    exit();
} else if ($phase === 'entered-code') {
    $code = trim($_POST['code'] ?? '');
    $redirect_result = db_verify_login_code($email, $code);
    if ($redirect_result !== null) {
        db_delete_login_codes_for_email($email);
        $token = create_secure_link('/login', [
            'phase' => 'select-user',
            'email' => $email,
            'redirect' => $redirect_result,
        ]);
        header('Location: ' . $token);
        exit();
    } else {
        error_log("Login code failed: email=" . $email . " code=[" . $code . "]");
        header('Location: /login?phase=enter-code&redirect=' . urlencode($redirect) . '&email=' . urlencode($email) . '&code=' . urlencode($code) . '&error_code=invalid-code');
        exit();
    }
}

if (is_logged_in()) {
  header('Location: /');
  exit;
}

if (ALLOW_ANONYMOUS_ACCESS && isset($_GET['skip-login'])) {
    make_anonymous_session();
    header('Location: ' . $redirect);
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
        'invalid-state' => 'An error occurred while logging in with registration. Please try again.',
        'no-code' => 'Unable to log in with registration. Please try again and make sure you click the "Authorize" button after reviewing the permissions.',
        'no-access' => 'Your membership does not include access to the online convention. If you think this is a mistake, please e-mail <a href="mailto:' . REGISTRATION_EMAIL . '">' . REGISTRATION_EMAIL . '</a>.',
        'under-age' => 'Your membership indicates you are under 16 and not allowed to access the online convention. If you think this is a mistake, please e-mail <a href="mailto:' . REGISTRATION_EMAIL . '">' . REGISTRATION_EMAIL . '</a>.<p>You can still access most of the portal by clicking "Continue without logging in", but you will not be able to access any content that is age restricted such as watching streams or joining the Discord.</p>',
        'duplicate-email' => 'An account with this e-mail address already exists. Please log in with your existing account.',
        'not-in-allowlist' => 'Sorry, the portal is not open for general access just yet. Watch for an email from the convention announcing it as open.',
        'incorrect-password' => 'The password you entered is incorrect. Please try again.',
        'invalid-code' => 'The code you entered is invalid or has expired. Please try again or send a new code.',
    ][$_GET['error_code']] ?? 'An unknown error occurred.';
?>
<?php
}
if (isset($error)) {
?>
    <p class="error"><img src="resources/error.svg" alt="error" aria-label="error"><?php echo $error; ?></p>
<?php
}
?>
<?php
if ($phase === 'enter-password') {
?>
    <form method="post" action="/login" class="nu-form">
        <input type="hidden" name="phase" value="entered-password">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
        <div class="form-group">
            <label for="email">Email</label>
            <p id="email"><?php echo htmlspecialchars($email); ?> <a href="/login?redirect=<?php echo urlencode($redirect); ?>">Change</a></p>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autofocus required>
            <span class="error-message"></span>
        </div>
        <button type="submit" class="button login">Log in</button>
    </form>
    <p>
        <form method="post" action="/forgot-password">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
            <button type="submit" class="button link">Forgotten your password?</button>
        </form>
    </p>
    <p><a href="/login?redirect=<?php echo urlencode($redirect); ?>&phase=requested-login-code&email=<?php echo isset($email) ? urlencode($email) : ''; ?>">Log in via email instead</a></p>
<?php
} else if ($phase === 'select-user') {
?>
    <p>Multiple members are associated with the e-mail address <?php echo htmlspecialchars($email); ?>. Please select your name from the list below to log in.</p>
<?php
    render_user_select($members, $redirect);
?>
<?php
} else if ($phase === 'enter-code') {
?>
    <p>A login code has been sent to <?php echo htmlspecialchars($_GET['email']); ?>. Please enter it below.</p>
    <p>If you do not receive the e-mail within a few minutes, please check your spam or junk mail folder.</p>
    <form method="post" action="/login" class="nu-form">
        <input type="hidden" name="phase" value="entered-code">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
        <div class="form-group">
            <label for="code">Login code</label>
            <input type="text" id="code" name="code" class="code-input" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autocomplete="one-time-code" autofocus required value="<?php echo htmlspecialchars($_GET['code'] ?? ''); ?>">
            <span class="error-message"></span>
        </div>
        <button type="submit" class="button login">Log in</button>
        <script>
        document.getElementById('code').addEventListener('input', function() {
            if (/^[0-9]{6}$/.test(this.value)) {
                this.form.submit();
            }
        });
        (function() {
            var input = document.getElementById('code');
            if (input.value.length > 0) {
                input.setSelectionRange(input.value.length, input.value.length);
            }
            var url = new URL(window.location);
            if (url.searchParams.has('code') || url.searchParams.has('error_code')) {
                url.searchParams.delete('code');
                url.searchParams.delete('error_code');
                history.replaceState(null, '', url);
            }
        })();
        </script>
    </form>
    <p><a href="/login?phase=requested-login-code&redirect=<?php echo urlencode($redirect); ?>&email=<?php echo urlencode($_GET['email']); ?>">Send a new code</a></p>
<?php
} else {
?>
    <form method="get" action="/login" class="nu-form">
        <input type="hidden" name="phase" value="entered-email" />
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" autofocus required></label></p>
            <span class="error-message"></span>
        </div>
        <button type="submit" class="button login">Next</button>
    </form>
<?php
}

if (ALLOW_ANONYMOUS_ACCESS) {
?>
    <p><a href="/login?skip-login&redirect=<?php echo urlencode($redirect); ?>">Continue without logging in</a></p>
<?php
}
?>
<?php
if ($phase === null || $phase === "enter-password") {
?>
    <p>Logging in to the member portal allows us to customise your online experience and give you quick access to the things most relevant to you. We also require you to log in to access streaming programme and chatting via the Discord.</p>
<?php
    if (time() < strtotime(CON_START_DATE)) {
?>
    <h3>Not a member?</h3>
    <p>If you&apos;re not yet a member of the convention, you can still <a href="https://eastercon2026.org/members/register">purchase a membership or ticket</a>.</p>
<?php
    }
?>
    <h3>Who can log in the portal?</h3>
    <p>People aged 7 and over who have Attending Membership or Supporting Membership can log in the portal.
    <?php
    if (time() < strtotime(CON_START_DATE)) {
    ?>
        If you don&apos;t already have one of these and would like to access the portal, you can <a href="https://eastercon2026.org/members/register">purchase a membership or ticket</a>.
    <?php
    }
    ?>
    <?php
if (ALLOW_ANONYMOUS_ACCESS) {
?>
    <p>If you do not wish to log in, you can click the "Continue without logging in" link to access most of the portal.</p>
<?php
}
}
?>
    <h3>Problems</h3>
    <p>Read the <a href="/login-instructions" target="_blank">instructions on how to log in</a>.</p>
    <p>If you are having trouble logging in, please e-mail <a href="mailto:<?php echo SUPPORT_EMAIL; ?>?subject=Trouble logging in to member portal"><?php echo SUPPORT_EMAIL; ?></a>.</p>
</article>

<?php

render_footer();

?>