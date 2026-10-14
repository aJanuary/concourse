<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

// $blocks is a list of ['type' => 'para'|'code'|'button', ...]. Paragraph text
// is Markdown. Returns whether the message was accepted by the SMTP server.
function send_email($to, $subject, $heading, $blocks, $from = SUPPORT_EMAIL) {
  require_config('smtp', 'host');

  $loader = new \Twig\Loader\FilesystemLoader(getenv('CONFIG_LIB_DIR') . '/templates');
  $twig = new \Twig\Environment($loader);
  $twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
    public function load($class) {
      if (MarkdownRuntime::class === $class) {
        return new MarkdownRuntime(new DefaultMarkdown());
      }
    }
  });
  $twig->addExtension(new \Twig\Extra\Markdown\MarkdownExtension());

  $body = $twig->load('email.html.twig')->render([
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
