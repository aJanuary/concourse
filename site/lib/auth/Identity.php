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
    // Role names to add to the member.
    public ?array $roles = null,
    // Roles the provider decides. Any of these missing from $roles are
    // removed from the member; other roles are left alone.
    public ?array $managed_roles = null,
    // The provider's ids for this login and user, used to end the session
    // when the provider says the user logged out.
    public ?string $sid = null,
    public ?string $sub = null,
    public ?string $id_token = null,
  ) {
  }
}
