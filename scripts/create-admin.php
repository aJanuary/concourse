<?php
//
// Gives a member the admin role, adding them to the members table first if
// they are not there. Run by scripts/create-admin, which explains the
// arguments.
//

require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

// Errors go to the terminal, not the site's HTML error page
restore_exception_handler();

function fail($message) {
  // STDERR is not defined when the script comes in on stdin
  file_put_contents('php://stderr', "$message\n");
  exit(1);
}

$args = array_slice($argv, 1);
if (count($args) !== 1 && count($args) !== 3) {
  fail('usage: scripts/create-admin <badge no> [<email> <name>]');
}
[$badge_no, $email, $name] = array_pad($args, 3, null);

$admin_role_id = db_get_role_id(ADMIN_ROLE_NAME);
if ($admin_role_id === null) {
  fail('There is no ' . ADMIN_ROLE_NAME . ' role; run scripts/db-migrate first');
}

if (db_get_member($badge_no)['email'] === null) {
  if ($email === null) {
    fail("There is no member $badge_no; give an email and name to add them");
  }
  db_create_member($badge_no, $email, $name);
  echo "Added member $badge_no\n";
} else if ($email !== null) {
  fail("Member $badge_no already exists; leave out the email and name");
}

if (db_grant_member_role($badge_no, $admin_role_id)) {
  echo "Gave $badge_no the " . ADMIN_ROLE_NAME . " role\n";
} else {
  echo "$badge_no already has the " . ADMIN_ROLE_NAME . " role\n";
}
