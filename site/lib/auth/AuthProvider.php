<?php

/*
 * Login providers
 *
 * A login method is an AuthProvider. Enable providers in the secrets file:
 *
 *   [auth]
 *   providers[] = registration-oauth
 *
 * When `providers[]` is absent only `registration-oauth` is enabled. The
 * `dev` provider is only ever offered when RUNTIME_ENV is "development".
 *
 * To add a provider, implement AuthProvider and add it to auth_provider_factories()
 * in auth/providers.php.
 *
 * Flow:
 *  1. login.php calls begin($redirect). The provider sends the browser
 *     elsewhere (or renders a form) and exits.
 *  2. The provider's callback or form submission calls complete($params).
 *     It returns an Identity, or throws AuthFailure with one of the error
 *     codes login.php knows how to display.
 *  3. The caller passes the Identity and the redirect to finish_login(),
 *     which creates or updates the member, starts the session and redirects.
 *
 * Providers never touch members or sessions themselves.
 */

require_once(__DIR__ . '/Identity.php');

interface AuthProvider {
  // Stable id used in config and URLs, e.g. "registration-oauth"
  public function id(): string;

  // Shown on the login button
  public function display_name(): string;

  // Starts a login. Must redirect or render a page, then exit.
  public function begin(string $redirect): void;

  // Finishes a login from callback or form parameters
  public function complete(array $params): Identity;
}
