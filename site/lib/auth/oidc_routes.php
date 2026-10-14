<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');

// The OIDC provider for a route under /oidc, or a 404 if it is not enabled
function oidc_provider_or_404() {
  $provider = auth_get_provider('oidc');
  if ($provider === null) {
    require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
    render_404();
  }
  return $provider;
}
