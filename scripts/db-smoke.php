<?php
//
// Exercises every db_* function against the configured database, including
// the writes, then removes what it created. Run inside the web container
// after migrations, with the same environment as the site:
//
//   php /var/scripts/db-smoke.php
//
// Works against either driver (CONFIG_DB_DRIVER). Exits non-zero on failure.
//

require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

$called = [];
$failures = 0;

function call($fn, ...$args) {
  global $called;
  $called[$fn] = true;
  return $fn(...$args);
}

function check($description, $condition) {
  global $failures;
  if ($condition) {
    echo "ok   $description\n";
  } else {
    echo "FAIL $description\n";
    $failures++;
  }
}

// Not reachable through the schema as migrated, so excluded from coverage:
// db_check_member_creds reads members.password and db_record_analytics writes
// the analytics table, neither of which exist.
$not_applicable = ['db_check_member_creds', 'db_record_analytics'];

$badge = 'SMK1';
$other_badge = 'SMK2';
$session = 'smoke-session-1';
$role_name = 'smoke-role';
$role_id = null;
$smoke_email = 'smoke.test@example.com';
$token = 'smoke-token';
$future = time() + 3600;

function cleanup() {
  global $badge, $other_badge, $role_id, $token;
  db_query("DELETE FROM login_codes WHERE email = 'smoke.test@example.com'");
  db_query("DELETE FROM login_attempts WHERE bucket = 'smoke'");
  db_query("DELETE FROM password_auth WHERE email = 'smoke.test@example.com'");
  db_query("DELETE FROM sessions WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM rce_invites WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM discord_ids WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM discord_verification_token WHERE token_id = ?", [$token]);
  db_query("DELETE FROM member_roles WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM roles WHERE name = 'smoke-role'");
  db_query("DELETE FROM members WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM prog_stages WHERE room_id = 'smoke-room'");
  db_query("DELETE FROM prog_sessions WHERE item_id = 'smoke-item'");
  db_query("DELETE FROM prog_discord_posts WHERE item_id IN ('smoke-post', 'smoke-post-2')");
  db_query("DELETE FROM prog_replay WHERE item_id = 'smoke-replay'");
}

echo "driver: " . DB_DRIVER . "\n";
cleanup();

$zoom_rows = db_fetch_value("SELECT COUNT(*) FROM prog_zoom");
$original_zoom = $zoom_rows ? db_get_zoom_url() : null;
$num_members_before = call('db_get_num_members');

try {
  // Members, and email lookups with different casing
  call('db_create_member', $badge, 'Smoke.Test@Example.com', 'Smoke Test');
  call('db_create_member', $other_badge, 'other@example.com', 'Other Member');
  check('member exists', call('db_member_exists', $badge) == 1);
  check('missing member does not exist', !call('db_member_exists', 'NOPE'));
  check('db_get_member', call('db_get_member', $badge) === ['name' => 'Smoke Test', 'email' => 'Smoke.Test@Example.com']);
  check('db_get_member for missing member', call('db_get_member', 'NOPE') === ['name' => null, 'email' => null]);
  call('db_update_member', $badge, 'smoke.test@example.com', 'Smoke Tester');
  check('db_update_member', call('db_get_member', $badge) === ['name' => 'Smoke Tester', 'email' => 'smoke.test@example.com']);
  check('member count is native int', call('db_get_num_members') === $num_members_before + 2);
  $members = array_column(call('db_get_members'), null, 'badge_no');
  check('db_get_members', $members[$badge] === ['badge_no' => $badge, 'name' => 'Smoke Tester', 'email' => 'smoke.test@example.com'] && isset($members[$other_badge]));

  // Members, passwords and login codes are looked up however the email is cased
  check('db_get_members_by_email ignores case', array_column(call('db_get_members_by_email', 'SMOKE.TEST@example.COM'), 'badge_no') === [$badge]);
  check('db_get_members_by_email for unknown email', call('db_get_members_by_email', 'nobody@example.com') === []);

  check('no password before one is set', call('db_email_has_password_auth', $smoke_email) === false);
  call('db_set_password_auth', 'Smoke.Test@example.com', 'correct horse');
  check('db_email_has_password_auth ignores case', call('db_email_has_password_auth', 'SMOKE.TEST@example.COM') === true);
  check('password accepted for differently-cased email', call('db_check_password_auth', 'SMOKE.TEST@example.COM', 'correct horse') === true);
  check('wrong password rejected', call('db_check_password_auth', $smoke_email, 'wrong') === false);
  check('password for unknown email rejected', call('db_check_password_auth', 'nobody@example.com', 'correct horse') === false);
  call('db_set_password_auth', $smoke_email, 'second password');
  check('setting a password again replaces it', call('db_check_password_auth', $smoke_email, 'second password') === true && call('db_check_password_auth', $smoke_email, 'correct horse') === false);

  call('db_create_login_code', 'Smoke.Test@example.com', 'hash-one', $future);
  check('wrong login code rejected', call('db_consume_login_code', $smoke_email, 'wrong', 3) === false);
  check('login code accepted after a wrong guess', call('db_consume_login_code', 'SMOKE.TEST@example.COM', 'hash-one', 3) === true);
  check('login code is single use', call('db_consume_login_code', $smoke_email, 'hash-one', 3) === false);
  call('db_create_login_code', $smoke_email, 'hash-two', $future);
  call('db_create_login_code', $smoke_email, 'hash-three', $future);
  check('only the newest login code works', call('db_consume_login_code', $smoke_email, 'hash-two', 3) === false);
  call('db_consume_login_code', $smoke_email, 'x', 3);
  call('db_consume_login_code', $smoke_email, 'x', 3);
  check('code is invalidated after too many failures', call('db_consume_login_code', $smoke_email, 'hash-three', 3) === false);
  call('db_create_login_code', $smoke_email, 'hash-expired', time() - 10);
  check('expired login code rejected', call('db_consume_login_code', $smoke_email, 'hash-expired', 3) === false);

  call('db_record_login_attempt', 'smoke', 'subject');
  call('db_record_login_attempt', 'smoke', 'subject');
  check('db_count_login_attempts', call('db_count_login_attempts', 'smoke', 'subject', 60) === 2);
  check('login attempts for another subject are not counted', call('db_count_login_attempts', 'smoke', 'other', 60) === 0);

  // Sessions
  call('db_insert_session', $session, $badge, $future);
  call('db_insert_session', 'smoke-expired', $other_badge, time() - 3600);
  check('db_session_exists', call('db_session_exists', $session) === true);
  check('expired session does not exist', call('db_session_exists', 'smoke-expired') === false);
  check('db_get_badge_no_by_session', call('db_get_badge_no_by_session', $session) === $badge);
  check('db_get_badge_no_by_session for missing session', call('db_get_badge_no_by_session', 'nope') === null);
  check('db_get_email_by_session', call('db_get_email_by_session', $session) === 'smoke.test@example.com');
  check('db_get_user_name_by_session', call('db_get_user_name_by_session', $session) === 'Smoke Tester');
  check('db_get_sessions', in_array(['badge_no' => $badge, 'name' => 'Smoke Tester', 'session_id' => $session], call('db_get_sessions'), true));

  // RCE invites
  call('db_create_invite', $badge, 'https://example.com/invite');
  check('db_get_magic_link', call('db_get_magic_link', $session) === 'https://example.com/invite');

  // Discord ids: second call is the upsert
  call('db_set_discord_id', $badge, '1001', 'first-name');
  call('db_set_discord_id', $badge, '1001', 'renamed');
  call('db_set_discord_id', $badge, '1002', 'second');
  check('db_insert_ignore inserts a new row', db_insert_ignore('discord_ids', ['badge_no' => $badge, 'discord_id' => '3001', 'username' => 'ignore-me']) === true);
  check('db_insert_ignore skips a conflicting row', db_insert_ignore('discord_ids', ['badge_no' => $badge, 'discord_id' => '3001', 'username' => 'changed']) === false);
  check('db_insert_ignore leaves the existing row alone', db_fetch_value("SELECT username FROM discord_ids WHERE badge_no = ? AND discord_id = '3001'", [$badge]) === 'ignore-me');
  db_query("DELETE FROM discord_ids WHERE discord_id = '3001'");
  check('db_get_discord_ids', call('db_get_discord_ids', $badge) === ['1001', '1002'] || call('db_get_discord_ids', $badge) === ['1002', '1001']);
  $usernames = call('db_get_discord_usernames', $session);
  sort($usernames);
  check('discord id upsert updates the username', $usernames === ['renamed', 'second']);
  $member_data = array_values(array_filter(call('db_get_member_discord_data'), fn($m) => $m['badge_no'] === $badge))[0];
  check('db_get_member_discord_data', count($member_data['discord_ids']) === 2 && $member_data['name'] === 'Smoke Tester');
  $other_data = array_values(array_filter(call('db_get_member_discord_data'), fn($m) => $m['badge_no'] === $other_badge))[0];
  check('db_get_member_discord_data for a member without discord ids', $other_data['discord_ids'] === []);

  // Roles and permissions
  $role_id = call('db_create_role', $role_name);
  check('db_create_role returns the new id as an int', is_int($role_id) && $role_id > 0);
  check('db_get_role_id', call('db_get_role_id', $role_name) === $role_id);
  check('db_get_roles', in_array($role_name, array_column(call('db_get_roles'), 'name'), true));
  call('db_add_member_role', $badge, $role_name);
  check('db_get_member_role_names', call('db_get_member_role_names', $badge) === [$role_name]);
  $permissions = call('db_get_role_permissions', $role_id);
  $permission_id = $permissions[0]['permission_id'];
  check('role starts with no permissions', array_sum(array_column($permissions, 'has_permission')) === 0);
  call('db_set_role_permission', $role_id, $permission_id, true);
  call('db_set_role_permission', $role_id, $permission_id, true);
  $permissions = call('db_get_role_permissions', $role_id);
  check('role permission set once however many times it is set', array_sum(array_column($permissions, 'has_permission')) === 1 && $permissions[0]['has_permission'] === 1);
  $permission_name = $permissions[0]['name'];
  check('db_get_permissions_by_session includes the role permission', in_array($permission_name, call('db_get_permissions_by_session', $session), true));
  check('db_get_default_permissions', is_array(call('db_get_default_permissions')));
  $discord_info = call('db_get_all_discord_info');
  check('db_get_all_discord_info', $discord_info['1001']['name'] === 'Smoke Tester' && $discord_info['1001']['roles'] === [$role_name]);
  call('db_set_role_permission', $role_id, $permission_id, false);
  check('role permission removed', array_sum(array_column(call('db_get_role_permissions', $role_id), 'has_permission')) === 0);
  db_query("DELETE FROM member_roles WHERE role_id = ?", [$role_id]);
  call('db_delete_role', $role_id);
  check('db_delete_role', call('db_get_role_id', $role_name) === null);

  // Discord verification tokens
  call('db_insert_discord_token', $token, '2001', 'token-user');
  check('db_get_discord_id_from_token', call('db_get_discord_id_from_token', $token) === 'token-user');
  check('unknown token', call('db_set_discord_id_from_token', $badge, 'nope') === ['result' => 'no-token']);
  check('token verified', call('db_set_discord_id_from_token', $badge, $token) === ['result' => 'verified', 'id' => '2001', 'username' => 'token-user']);
  check('token already verified', call('db_set_discord_id_from_token', $badge, $token) === ['result' => 'already-verified', 'id' => '2001', 'username' => 'token-user']);
  check('verified token linked the discord id', in_array('2001', call('db_get_discord_ids', $badge), true));

  // Programme stages
  call('db_add_stage', 'smoke-room', 'Smoke Stage', 'https://example.com/v', 'hybrid', 'https://example.com/p');
  check('db_get_stage', call('db_get_stage', 'smoke-room') === ['title' => 'Smoke Stage', 'viewer_url' => 'https://example.com/v', 'type' => 'hybrid', 'participant_url' => 'https://example.com/p']);
  call('db_edit_stage', 'smoke-room', 'Smoke Stage 2', 'https://example.com/v2', 'online-only', 'https://example.com/p2');
  check('db_edit_stage', call('db_get_stage', 'smoke-room')['type'] === 'online-only');
  check('db_get_stages', in_array(['room_id' => 'smoke-room', 'title' => 'Smoke Stage 2'], call('db_get_stages'), true));
  call('db_delete_stage', 'smoke-room');
  check('db_delete_stage', call('db_get_stage', 'smoke-room')['title'] === null);

  // Programme sessions
  call('db_add_prog_session', 'smoke-item', 'Smoke Session', 'https://example.com/s');
  check('db_get_prog_session', call('db_get_prog_session', 'smoke-item') === ['title' => 'Smoke Session', 'rce_url' => 'https://example.com/s']);
  call('db_edit_prog_session', 'smoke-item', 'Smoke Session 2', 'https://example.com/s2');
  check('db_edit_prog_session', call('db_get_prog_session', 'smoke-item')['title'] === 'Smoke Session 2');
  check('db_get_prog_sessions', in_array(['item_id' => 'smoke-item', 'title' => 'Smoke Session 2'], call('db_get_prog_sessions'), true));
  call('db_delete_prog_session', 'smoke-item');
  check('db_delete_prog_session', call('db_get_prog_session', 'smoke-item')['title'] === null);

  // Discord posts
  call('db_add_discord_post', 'smoke-post', 'Smoke Post', '2025-01-01 10:00', '60', 'smoke-room', 'https://example.com/post');
  check('db_get_discord_post', call('db_get_discord_post', 'smoke-post') === ['start' => '2025-01-01 10:00', 'title' => 'Smoke Post', 'duration' => 60, 'room_id' => 'smoke-room', 'post_url' => 'https://example.com/post']);
  $start = (new DateTime('2025-01-01 10:30', new DateTimeZone(TIMEZONE)))->getTimestamp();
  check('db_get_discord_post_by_room_and_time', call('db_get_discord_post_by_room_and_time', 'smoke-room', $start)['title'] === 'Smoke Post');
  check('no discord post outside its time', call('db_get_discord_post_by_room_and_time', 'smoke-room', $start + 7200)['title'] === null);
  call('db_edit_discord_post', 'smoke-post', 'Smoke Post 2', '2025-01-01 11:00', '30', 'smoke-room', 'https://example.com/post2');
  check('db_edit_discord_post', call('db_get_discord_post', 'smoke-post')['duration'] === 30);
  call('db_upsert_discord_post', 'smoke-post', 'Ignored Title', '2025-01-01 12:00', '45', 'smoke-room-2', 'https://example.com/post3');
  $post = call('db_get_discord_post', 'smoke-post');
  check('discord post upsert updates an existing post but keeps its title', $post['title'] === 'Smoke Post 2' && $post['duration'] === 45 && $post['room_id'] === 'smoke-room-2');
  call('db_upsert_discord_post', 'smoke-post-2', 'New Post', null, null, null, null);
  check('discord post upsert inserts a new post', call('db_get_discord_post', 'smoke-post-2')['title'] === 'New Post');
  check('db_get_discord_posts', in_array(['item_id' => 'smoke-post', 'title' => 'Smoke Post 2'], call('db_get_discord_posts'), true));
  call('db_delete_discord_post', 'smoke-post');
  call('db_delete_discord_post', 'smoke-post-2');
  check('db_delete_discord_post', call('db_get_discord_post', 'smoke-post')['title'] === null);

  // Zoom url (a single-row table, left as found)
  if (!$zoom_rows) {
    db_query("INSERT INTO prog_zoom (zoom_url) VALUES ('https://example.com/zoom-original')");
  }
  call('db_set_zoom_url', 'https://example.com/zoom');
  check('db_get_zoom_url', call('db_get_zoom_url') === 'https://example.com/zoom');
  if ($zoom_rows) {
    db_set_zoom_url($original_zoom);
  } else {
    db_query("DELETE FROM prog_zoom");
  }

  // Replays
  call('db_add_replay', 'smoke-replay', 'Smoke Replay', 'https://example.com/r');
  check('db_get_replay', call('db_get_replay', 'smoke-replay') === ['title' => 'Smoke Replay', 'replay_url' => 'https://example.com/r']);
  call('db_edit_replay', 'smoke-replay', 'Smoke Replay 2', 'https://example.com/r2');
  check('db_edit_replay', call('db_get_replay', 'smoke-replay')['title'] === 'Smoke Replay 2');
  check('db_get_replays', in_array(['item_id' => 'smoke-replay', 'title' => 'Smoke Replay 2'], call('db_get_replays'), true));
  call('db_delete_replay', 'smoke-replay');
  check('db_delete_replay', call('db_get_replay', 'smoke-replay')['title'] === null);

  // Sessions last, as the earlier checks depend on them
  call('db_delete_session', $session);
  check('db_delete_session', call('db_session_exists', $session) === false);
} finally {
  cleanup();
}

$all = array_filter(get_defined_functions()['user'], fn($f) => strpos($f, 'db_') === 0);
$data_functions = array_filter($all, fn($f) => !in_array($f, [
  // helpers and dialect functions, exercised through the functions above
  'db_dsn', 'db_query', 'db_fetch_value', 'db_fetch_row', 'db_fetch_rows', 'db_fetch_column',
  'db_driver', 'db_quote_identifier', 'db_upsert', 'db_insert_ignore', 'db_last_insert_id',
  'db_format_discord_post',
]));
$uncovered = array_diff($data_functions, array_keys($called), $not_applicable);
echo "\ncovered " . count(array_intersect($data_functions, array_keys($called))) . " of " . count($data_functions) . " db_* functions\n";
echo "not applicable to the migrated schema: " . implode(', ', $not_applicable) . "\n";
if ($uncovered) {
  echo "UNCOVERED: " . implode(', ', $uncovered) . "\n";
  $failures++;
}

echo $failures ? "\n$failures FAILED\n" : "\nall passed\n";
exit($failures ? 1 : 0);
