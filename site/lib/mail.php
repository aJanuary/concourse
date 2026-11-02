<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// $blocks is a list of ['type' => 'para'|'code'|'button', ...]. Paragraph text
// is Markdown. Returns whether the message was accepted by the SMTP server.
function send_email($to, $subject, $heading, $blocks, $from = SUPPORT_EMAIL) {
  require_config('smtp', 'host');

  $body = twig()->render('email/email.html.twig', [
    'con_name' => CON_NAME,
    'con_website' => CON_WEBSITE,
    'heading' => $heading,
    'blocks' => $blocks,
  ]);

  $plaintext_parts = ["**" . $heading . "**", "\n"];
  foreach ($blocks as $block) {
    if ($block['type'] == 'para' || $block['type'] == 'code') {
      $plaintext_parts[] = $block['text'];
    } else if ($block['type'] == 'button') {
      $plaintext_parts[] = $block['href'];
    }
    $plaintext_parts[] = "\n";
  }

  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = SMTP_USERNAME !== '';
    $mail->Username = SMTP_USERNAME;
    $mail->Password = config_get_secret('smtp', 'password');
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port = SMTP_PORT;

    $mail->setFrom($from, CON_NAME);
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = implode("\n", $plaintext_parts);

    $mail->send();
    return true;
  } catch (Exception $e) {
    error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    return false;
  }
}
