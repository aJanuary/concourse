<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (current_user_has_permission('see-newsletter-redirect')) {
  header('Location: ' . 'https://example.com/newsletter');
  exit;
}

render_page('pages/newsletter.html.twig', [
  'title' => 'Newsletter',
  'description' => 'Newsletter for ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Newsletter'],
]);
