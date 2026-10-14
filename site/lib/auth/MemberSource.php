<?php

// Where the portal finds members in the registration system: the local login
// provider by email, and the site selection desk by searching.
//
// The default reads this portal's own members table, so only finds members
// who have logged in. An install whose members live in a registration system
// can supply an implementation that queries it, by changing
// auth_member_source() in providers.php.
interface MemberSource {
  // Members identified by the email, case-insensitively, each as
  // ['badge_no' => ..., 'name' => ..., 'email' => ...]. Empty when none.
  public function members_for_email(string $email): array;

  // Members whose badge number is the query, or whose name or email contains
  // it, case-insensitively, in the same form as members_for_email. At most
  // MEMBER_SOURCE_SEARCH_LIMIT of them.
  public function search(string $query): array;
}

const MEMBER_SOURCE_SEARCH_LIMIT = 200;
