<?php

// Where the local login provider finds the members behind an email address.
//
// The default reads this portal's own members table. An install whose members
// live in a registration system can supply an implementation that queries it,
// by changing auth_member_source() in providers.php.
interface MemberSource {
  // Members identified by the email, case-insensitively, each as
  // ['badge_no' => ..., 'name' => ..., 'email' => ...]. Empty when none.
  public function members_for_email(string $email): array;
}
