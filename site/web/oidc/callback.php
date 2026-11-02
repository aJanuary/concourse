<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/oidc_routes.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

use chillerlan\QRCode\QRCode;

$provider = oidc_provider_or_404();

if (!$provider->login_in_progress()) {
  $url = ROOT_URL . $_SERVER['REQUEST_URI'];
  render_page('pages/oidc/callback.html.twig', [
    'title' => 'Log in',
    'description' => 'Log in to the member portal for ' . CON_NAME . '.',
    'breadcrumbs' => ['Home' => '/', 'Log in'],
    'url' => $url,
    'qr_code' => (new QRCode)->render($url),
  ]);
  exit;
}

$redirect = $provider->pending_redirect();
try {
  finish_login($provider->complete($_GET), $redirect);
} catch (AuthFailure $e) {
  redirect_to_login_error($e->getMessage(), $redirect);
}
