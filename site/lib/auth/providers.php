<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(__DIR__ . '/AuthProvider.php');

// Providers enabled when `[auth] providers[]` is not set: what existed before
// providers were configurable.
const DEFAULT_AUTH_PROVIDERS = ['registration-oauth'];

// Constructors are deferred so a provider's dependencies are only loaded when it is enabled
function auth_provider_factories() {
  return [
    'registration-oauth' => function () {
      require_once(__DIR__ . '/RegistrationOAuthProvider.php');
      return new RegistrationOAuthProvider();
    },
    'dev' => function () {
      require_once(__DIR__ . '/DevProvider.php');
      return DevProvider::is_available() ? new DevProvider() : null;
    },
  ];
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
