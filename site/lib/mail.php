<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_email($to, $subject, $body, $from = SUPPORT_EMAIL) {
    $mail = new PHPMailer(true);
    error_log(SMTP_HOST);
    error_log(SMTP_PORT);
    error_log(SMTP_USERNAME);
    error_log(SMTP_PASSWORD);
    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = SMTP_USERNAME !== '';
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE ?? '';
        $mail->Port       = SMTP_PORT;

        $mail->setFrom($from, CON_NAME);
        $mail->addAddress($to);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>