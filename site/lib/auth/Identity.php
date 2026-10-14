<?php

// Thrown by providers and finish_login() when a login cannot proceed.
// The message is an error code understood by login.php.
class AuthFailure extends Exception {
}

// Who a provider says is logging in
class Identity {
  public function __construct(
    public string $badge_no,
    public string $email,
    public string $name,
    public ?string $real_name = null,
    // Role names to add to the member. Existing roles are never removed.
    public ?array $roles = null,
  ) {
  }
}
