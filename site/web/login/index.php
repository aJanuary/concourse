<?php
require_once('../../includes/config.php');
require_once('../../includes/auth_functions.php');
require_once('../../includes/db.php');
require_once('../../includes/template.php');
require_once('../../includes/mail.php');

if (is_logged_in()) {
    header('Location: /');
    exit;
}

function render_login_form($error=false) {
?>
    <form hx-post="/login/" hx-swap="innerHTML" hx-ext="loading-states" data-loading-disable data-loading-aria-busy>
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

function send_login_link($email) {
    $login_code = bin2hex(random_bytes(16));
    $subject = 'Log in to ' . CON_NAME . ' member portal';
    $plaintext_message = "Hello,\n\nYou requested to log in to " . CON_NAME . ".\n\nTo log in, visit " . ROOT_URL . "/login/?email=" . $email . "&login_code=" . $login_code . "\n\nThis link is just for you. Do not share it with anyone.\n\nIf you did not request this invitation, please ignore this email.\n\nBest wishes,\n\nThe " . CON_NAME . " team";
    $html_message = "<p>Hello,</p><p>You requested to log in to " . CON_NAME . ".</p><p>To log in, visit <a href=\"" . ROOT_URL . "/login/?email=" . $email . "&login_code=" . $login_code . "\">" . ROOT_URL . "/login/?email=" . $email . "&login_code=" . $login_code . "</a>.</p><p>This link is just for you. Do not share it with anyone.</p><p>If you did not request this invitation, please ignore this email.</p><p>Best wishes,</p><p>The " . CON_NAME . " team</p>";
    db_insert_login_link($email, $login_code, time() + 60*60*24*5);
    send_email($email, $subject, $plaintext_message, $html_message);
}

function render_login_link_form($email) {
?>
    <p>A log in link has been sent to <?php echo $email; ?>.</p>
    <p>Please check your email and follow the instructions to log in.</p>
<?php
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $reg_status = get_registration_status($email);
    if ($reg_status == "registered") {
        send_login_link($email);
        render_login_link_form($email);
    } else if ($reg_status == "pending") {
        render_login_form('We are still processing your registration. Please try again later.');
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
        $session_id = sha1(rand());
        $expires_at = time() + 60*60*24*30;
        db_insert_session($session_id, $email, $expires_at);
        setcookie("session", $session_id, $expires_at, '/', '', true, true);
        header('Location: /');
        exit();
    } else if ($link_status === "expired") {
        $error = 'This log in link has expired. Please request a new log in link.';
    } else {
        $error = 'Invalid login code';
    }
}

render_header();

?>

<article>
    <h3>Log in</h3>
    <p>If you are having trouble logging in, please e-mail <a href="mailto:<?php echo EMAIL; ?>?subject=Trouble logging in to member portal"><?php echo EMAIL; ?></a>.</p>
    <?php render_login_form($error); ?>
</article>

<?php

render_footer();

?>