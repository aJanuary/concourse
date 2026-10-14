<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(__DIR__ . '/AuthProvider.php');

// Providers enabled when `[auth] providers[]` is not set. `dev` only exists in
// development, so a production install must configure a provider.
const DEFAULT_AUTH_PROVIDERS = ['dev'];

// Constructors are deferred so a provider's dependencies are only loaded when it is enabled
function auth_provider_factories() {
  return [
    'local' => function () {
      require_once(__DIR__ . '/LocalProvider.php');
      return new LocalProvider();
    },
    'oidc' => function () {
      require_once(__DIR__ . '/OidcProvider.php');
      return new OidcProvider();
    },
    'dev' => function () {
      require_once(__DIR__ . '/DevProvider.php');
      return DevProvider::is_available() ? new DevProvider() : null;
    },
  ];
}

// Where the local provider finds members. Replace this to look members up in
// a registration system instead of the members table.
function auth_member_source() {
  require_once(__DIR__ . '/DbMemberSource.php');
  return new DbMemberSource();
}

// Enabled providers in the order configured
function auth_enabled_providers() {
  static $providers = null;
  if ($providers !== null) {
    return $providers;
  }

  $factories = auth_provider_factories();
  $providers = [];
  foreach (AUTH_PROVIDERS ?: DEFAULT_AUTH_PROVIDERS as $id) {
    if (!isset($factories[$id])) {
      throw new RuntimeException("Unknown login provider '$id' in the [auth] providers setting");
    }
    $provider = $factories[$id]();
    if ($provider !== null) {
      $providers[$id] = $provider;
    }
  }
  return $providers;
}

function auth_get_provider($id) {
  return auth_enabled_providers()[$id] ?? null;
}
