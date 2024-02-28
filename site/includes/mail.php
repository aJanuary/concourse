<?php

require_once('config.php');
require_once('secrets.php');
require_once('../../external/swiftmailer-5.4.8/lib/swift_required.php');

function send_email($to, $subject, $text_body, $html_body) {
  $transport = (new Swift_SmtpTransport(SMTP_ADDRESS, SMTP_PORT, 'SSL'))
    ->setUsername(SMTP_USER)
    ->setPassword(SMTP_PASSWORD);
  $mailer = new Swift_Mailer($transport);

  $message = new Swift_Message($subject);
  $message->setBody($html_body,'text/html');
  $message->addPart($text_body, 'text/plain', 'utf-8');
  $message->setReplyTo(EMAIL);
  $message->setFrom([ EMAIL => CON_NAME ]);

  $message->setTo($to);
  $mailer->send($message);
}

?>