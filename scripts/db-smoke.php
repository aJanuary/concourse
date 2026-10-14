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
  db_query("DELETE FROM password_prompt_dismissals WHERE email = 'smoke.test@example.com'");
  db_query("DELETE FROM oidc_sessions WHERE sub = 'smoke-sub'");
  db_query("DELETE FROM sessions WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM discord_ids WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM discord_verification_token WHERE token_id = ?", [$token]);
  db_query("DELETE FROM member_roles WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM roles WHERE name = 'smoke-role'");
  db_query("DELETE FROM site_selection_tokens WHERE token LIKE 'smoke-ss-%' OR (token IS NULL AND badge_no IN (?, ?))", [$badge, $other_badge]);
  db_query("UPDATE site_selection_tokens SET badge_no = NULL, method = NULL, sold_by = NULL, sold_at = NULL, spoilt_at = NULL WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM site_selection_payment_links WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM site_selection_info WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM members WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM prog_stages WHERE room_id = 'smoke-room'");
  db_query("DELETE FROM prog_sessions WHERE item_id = 'smoke-item'");
  db_query("DELETE FROM prog_stream_urls WHERE room_id = 'smoke-room'");
  db_query("DELETE FROM zoom_events_tickets WHERE badge_no IN (?, ?)", [$badge, $other_badge]);
  db_query("DELETE FROM auth_token_cache WHERE service = 'smoke'");
  db_query("DELETE FROM prog_discord_posts WHERE item_id IN ('smoke-post', 'smoke-post-2')");
  db_query("DELETE FROM prog_replay WHERE item_id = 'smoke-replay'");
  db_query("DELETE FROM signups WHERE category = 'smoke-category'");
  db_query("DELETE FROM business_meeting_links WHERE badge_no IN (?, ?, 'SMK3')", [$badge, $other_badge]);
}

echo "driver: " . DB_DRIVER . "\n";
cleanup();

$zoom_rows = db_fetch_value("SELECT COUNT(*) FROM prog_zoom");
$original_zoom = $zoom_rows ? db_get_zoom_url() : null;
$num_members_before = call('db_get_num_members');

try {
  // Members, and email lookups with different casing
  call('db_create_member', $badge, 'Smoke.Test@Example.com', 'Smoke Test', 'Smoke Real');
  call('db_create_member', $other_badge, 'other@example.com', 'Other Member');
  check('member exists', call('db_member_exists', $badge) == 1);
  check('missing member does not exist', !call('db_member_exists', 'NOPE'));
  check('db_get_member', call('db_get_member', $badge) === ['name' => 'Smoke Test', 'email' => 'Smoke.Test@Example.com', 'real_name' => 'Smoke Real']);
  check('db_get_member for missing member', call('db_get_member', 'NOPE') === ['name' => null, 'email' => null, 'real_name' => null]);
  call('db_update_member', $badge, 'smoke.test@example.com', 'Smoke Tester');
  check('db_update_member keeps the real name when none is given', call('db_get_member', $badge) === ['name' => 'Smoke Tester', 'email' => 'smoke.test@example.com', 'real_name' => 'Smoke Real']);
  call('db_update_member', $badge, 'smoke.test@example.com', 'Smoke Tester', 'Smoke Realer');
  check('db_update_member', call('db_get_member', $badge)['real_name'] === 'Smoke Realer');
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

  check('password prompt not dismissed at first', call('db_password_prompt_dismissed', $smoke_email) === false);
  call('db_dismiss_password_prompt', 'Smoke.Test@example.com');
  call('db_dismiss_password_prompt', $smoke_email);
  check('db_dismiss_password_prompt ignores case and repeats', call('db_password_prompt_dismissed', 'SMOKE.TEST@example.COM') === true);

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

  // OIDC sessions, ended by the provider's sid or sub
  db_insert_session('smoke-oidc-1', $other_badge, $future);
  db_insert_session('smoke-oidc-2', $other_badge, $future);
  call('db_insert_oidc_session', 'smoke-oidc-1', 'smoke-sid', 'smoke-sub', 'id-token-1');
  call('db_insert_oidc_session', 'smoke-oidc-2', null, 'smoke-sub', null);
  check('db_oidc_session_exists', call('db_oidc_session_exists', 'smoke-oidc-1') === true);
  check('db_oidc_session_exists for a non-OIDC session', call('db_oidc_session_exists', $session) === false);
  check('db_get_oidc_id_token', call('db_get_oidc_id_token', 'smoke-oidc-1') === 'id-token-1');
  check('db_get_oidc_id_token for a non-OIDC session', call('db_get_oidc_id_token', $session) === null);
  check('db_delete_sessions_by_sid', call('db_delete_sessions_by_sid', 'smoke-sid') === 1 && !db_session_exists('smoke-oidc-1') && db_session_exists('smoke-oidc-2'));
  check('db_delete_sessions_by_sub', call('db_delete_sessions_by_sub', 'smoke-sub') === 1 && !db_session_exists('smoke-oidc-2'));
  check('OIDC sessions go with their sessions', db_fetch_value("SELECT COUNT(*) FROM oidc_sessions WHERE sub = 'smoke-sub'") == 0);
  check('db_session_exists', call('db_session_exists', $session) === true);
  check('expired session does not exist', call('db_session_exists', 'smoke-expired') === false);
  check('db_get_badge_no_by_session', call('db_get_badge_no_by_session', $session) === $badge);
  check('db_get_badge_no_by_session for missing session', call('db_get_badge_no_by_session', 'nope') === null);
  check('db_get_email_by_session', call('db_get_email_by_session', $session) === 'smoke.test@example.com');
  check('db_get_user_name_by_session', call('db_get_user_name_by_session', $session) === 'Smoke Tester');
  check('db_get_sessions', in_array(['badge_no' => $badge, 'name' => 'Smoke Tester', 'session_id' => $session], call('db_get_sessions'), true));

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
  call('db_remove_member_role', $badge, $role_name);
  check('db_remove_member_role', db_get_member_role_names($badge) === []);
  db_add_member_role($badge, $role_name);
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
  check('db_get_member_permissions_without_role leaves out the role', !in_array($permission_name, call('db_get_member_permissions_without_role', $badge, $role_id), true));
  check('db_get_member_permissions_without_role keeps other roles', in_array($permission_name, call('db_get_member_permissions_without_role', $badge, $role_id + 1000), true));

  // Roles by id, as the member roles admin uses them
  check('db_get_role', call('db_get_role', $role_id) === ['role_id' => $role_id, 'name' => $role_name]);
  check('db_get_role for missing role', call('db_get_role', 0) === null);
  check('db_get_member_roles', call('db_get_member_roles', $badge) === [['role_id' => $role_id, 'name' => $role_name]]);
  check('db_grant_member_role for a role the member has', call('db_grant_member_role', $badge, $role_id) === false);
  check('db_revoke_member_role', call('db_revoke_member_role', $badge, $role_id) === true && call('db_get_member_roles', $badge) === []);
  check('db_revoke_member_role for a role the member lacks', call('db_revoke_member_role', $badge, $role_id) === false);
  check('db_grant_member_role', call('db_grant_member_role', $badge, $role_id) === true && count(call('db_get_member_roles', $badge)) === 1);

  // Member search
  check('db_search_members by badge number', array_column(call('db_search_members', $badge), 'badge_no') === [$badge]);
  check('db_search_members by name ignores case', array_column(call('db_search_members', 'smoke TESTER'), 'badge_no') === [$badge]);
  check('db_search_members by email', array_column(call('db_search_members', 'other@example'), 'badge_no') === [$other_badge]);
  check('db_search_members treats wildcards literally', call('db_search_members', 'Smoke%Tester') === [] && call('db_search_members', 'Smoke_Tester') === []);

  // The admin role has every permission without any being granted
  $admin_role_id = db_get_role_id(ADMIN_ROLE_NAME);
  $admin_permissions = db_get_role_permissions($admin_role_id);
  check('admin role has every permission', count($admin_permissions) > 0 && array_sum(array_column($admin_permissions, 'has_permission')) === count($admin_permissions));
  db_grant_member_role($other_badge, $admin_role_id);
  db_insert_session('smoke-admin-session', $other_badge, time() + 3600);
  $all_permissions = array_column($admin_permissions, 'name');
  $session_permissions = db_get_permissions_by_session('smoke-admin-session');
  sort($all_permissions);
  sort($session_permissions);
  check('admin member has every permission', $session_permissions === $all_permissions);
  db_delete_session('smoke-admin-session');
  db_revoke_member_role($other_badge, $admin_role_id);

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
  call('db_add_prog_session', 'smoke-item', 'Smoke Session', 'https://example.com/s', null);
  check('db_get_prog_session', call('db_get_prog_session', 'smoke-item') === ['found' => true, 'title' => 'Smoke Session', 'destination' => 'https://example.com/s', 'handler' => null]);
  call('db_edit_prog_session', 'smoke-item', 'Smoke Session 2', 'smoke-room', 'stream');
  $prog_session = call('db_get_prog_session', 'smoke-item');
  check('db_edit_prog_session', $prog_session['title'] === 'Smoke Session 2' && $prog_session['handler'] === 'stream');
  call('db_upsert_prog_session', 'smoke-item', 'Smoke Session 3', 'room-2', 'discord');
  call('db_upsert_prog_session', 'smoke-item-2', 'New Session', 'room-3', null);
  check('prog session upsert updates and inserts', call('db_get_prog_session', 'smoke-item')['handler'] === 'discord' && call('db_get_prog_session', 'smoke-item-2')['title'] === 'New Session');
  check('db_get_prog_sessions', in_array(['item_id' => 'smoke-item', 'title' => 'Smoke Session 3'], call('db_get_prog_sessions'), true));
  call('db_delete_prog_session', 'smoke-item');
  call('db_delete_prog_session', 'smoke-item-2');
  check('db_delete_prog_session', call('db_get_prog_session', 'smoke-item') === ['found' => false, 'title' => null, 'destination' => null, 'handler' => null]);

  // Stream URLs
  call('db_add_stream_url', 'smoke-room', 'Smoke Room', null);
  check('db_get_stream_url', call('db_get_stream_url', 'smoke-room') === ['found' => true, 'title' => 'Smoke Room', 'url' => null]);
  call('db_edit_stream_url', 'smoke-room', 'Smoke Room 2', 'https://example.com/stream');
  check('db_edit_stream_url', call('db_get_stream_url', 'smoke-room')['url'] === 'https://example.com/stream');
  check('db_get_stream_urls', in_array(['room_id' => 'smoke-room', 'title' => 'Smoke Room 2', 'url' => 'https://example.com/stream'], call('db_get_stream_urls'), true));
  call('db_delete_stream_url', 'smoke-room');
  check('db_delete_stream_url', call('db_get_stream_url', 'smoke-room')['found'] === false);

  // Zoom Events tickets and cached auth tokens
  call('db_save_zoom_events_ticket', $badge, 'ticket-1', 'https://example.com/join-1', 'a@example.com');
  call('db_save_zoom_events_ticket', $badge, 'ticket-2', 'https://example.com/join-2', 'b@example.com');
  check('db_get_zoom_events_ticket keeps the first ticket', call('db_get_zoom_events_ticket', $badge)['ticket_id'] === 'ticket-1');
  check('no zoom events ticket', call('db_get_zoom_events_ticket', $other_badge) === null);
  call('db_cache_auth_token', 'smoke', 'token-1', $future);
  call('db_cache_auth_token', 'smoke', 'token-2', $future + 10);
  check('db_get_cached_auth_token', call('db_get_cached_auth_token', 'smoke') === ['auth_token' => 'token-2', 'expires_at' => $future + 10]);
  check('no cached auth token', call('db_get_cached_auth_token', 'smoke-other') === null);

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
  call('db_add_replay', 'smoke-replay', 'Smoke Replay', 'https://example.com/r', false);
  check('db_get_replay', call('db_get_replay', 'smoke-replay') === ['found' => true, 'title' => 'Smoke Replay', 'replay_url' => 'https://example.com/r', 'unavailable' => false]);
  call('db_edit_replay', 'smoke-replay', 'Smoke Replay 2', 'https://example.com/r2', true);
  $replay = call('db_get_replay', 'smoke-replay');
  check('db_edit_replay', $replay['title'] === 'Smoke Replay 2' && $replay['unavailable'] === true);
  check('db_get_replays', in_array(['item_id' => 'smoke-replay', 'title' => 'Smoke Replay 2'], call('db_get_replays'), true));
  call('db_delete_replay', 'smoke-replay');
  check('db_delete_replay', call('db_get_replay', 'smoke-replay')['found'] === false);

  // Signups
  $signup = ['title' => 'Smoke Signup', 'category' => 'smoke-category', 'url' => 'https://example.com/form?n={name}', 'guide_url' => null, 'notes' => null, 'session_start' => 1700000000, 'opens_at' => null, 'closes_at' => 4102444800];
  $signup_id = call('db_add_signup', $signup);
  check('db_add_signup returns the new id as an int', is_int($signup_id) && $signup_id > 0);
  check('db_get_signup', call('db_get_signup', $signup_id) === ['id' => $signup_id] + $signup);
  check('db_get_signup for missing signup', call('db_get_signup', -1) === null);
  call('db_edit_signup', $signup_id, ['notes' => "Two\nlines", 'opens_at' => 1600000000] + $signup);
  $edited = call('db_get_signup', $signup_id);
  check('db_edit_signup', $edited['notes'] === "Two\nlines" && $edited['opens_at'] === 1600000000 && $edited['closes_at'] === 4102444800);
  call('db_add_signups', [['title' => 'Bulk 1'] + $signup, ['title' => 'Bulk 2'] + $signup]);
  check('db_add_signups', count(array_filter(call('db_get_signups'), fn($s) => $s['category'] === 'smoke-category')) === 3);
  $failed = false;
  try {
    db_add_signups([['title' => 'Bulk 3'] + $signup, ['title' => null] + $signup]);
  } catch (PDOException $e) {
    $failed = true;
  }
  check('db_add_signups adds nothing when a row fails', $failed && count(array_filter(db_get_signups(), fn($s) => $s['category'] === 'smoke-category')) === 3);
  call('db_delete_signup', $signup_id);
  check('db_delete_signup', db_get_signup($signup_id) === null);

  // Business meeting links. SMK3 has no member, so no name
  call('db_set_business_meeting_link', $badge, 'https://example.com/join?a="<1>');
  check('db_get_business_meeting_link', call('db_get_business_meeting_link', $badge) === 'https://example.com/join?a="<1>');
  check('db_get_business_meeting_link for member without a link', db_get_business_meeting_link($other_badge) === null);
  call('db_set_business_meeting_link', $badge, 'https://example.com/join/1b');
  check('db_set_business_meeting_link updates', db_get_business_meeting_link($badge) === 'https://example.com/join/1b');
  $counts = call('db_set_business_meeting_links', [$badge => 'https://example.com/join/1c', 'SMK3' => 'https://example.com/join/3']);
  check('db_set_business_meeting_links counts', $counts === ['added' => 1, 'updated' => 1]);
  $bm_links = array_values(array_filter(call('db_get_business_meeting_links'), fn($l) => in_array($l['badge_no'], [$badge, 'SMK3'], true)));
  check('db_get_business_meeting_links', count($bm_links) === 2
    && $bm_links[0]['badge_no'] === $badge && $bm_links[0]['name'] === 'Smoke Tester' && $bm_links[0]['join_link'] === 'https://example.com/join/1c' && is_int($bm_links[0]['updated_at'])
    && $bm_links[1]['badge_no'] === 'SMK3' && $bm_links[1]['name'] === null);
  $failed = false;
  try {
    db_set_business_meeting_links([$other_badge => 'https://example.com/join/2', 'SMK3' => null]);
  } catch (PDOException $e) {
    $failed = true;
  }
  check('db_set_business_meeting_links adds nothing when a row fails', $failed && db_get_business_meeting_link($other_badge) === null && db_get_business_meeting_link('SMK3') === 'https://example.com/join/3');
  call('db_delete_business_meeting_link', $badge);
  check('db_delete_business_meeting_link', db_get_business_meeting_link($badge) === null);

  // Site selection. Other keys may already be in the pool, so checks don't
  // assume which key is taken, and no_tokens is only checked if there are none.
  $pool_before = call('db_count_available_site_selection_tokens');
  call('db_add_site_selection_tokens', [
    ['label' => 'smoke-label A', 'token' => 'smoke-ss-Aa'],
    ['label' => 'smoke-label B', 'token' => 'smoke-ss-aa'],
    ['label' => 'smoke-label C', 'token' => 'smoke-ss-c'],
  ], $badge);
  check('db_count_available_site_selection_tokens', db_count_available_site_selection_tokens() === $pool_before + 3);
  check('db_get_existing_site_selection_tokens is case sensitive', call('db_get_existing_site_selection_tokens', ['smoke-ss-Aa', 'smoke-ss-AA', 'smoke-ss-new']) === ['smoke-ss-Aa']);
  $failed = false;
  try {
    db_add_site_selection_tokens([['label' => 'smoke-label D', 'token' => 'smoke-ss-d'], ['label' => 'smoke-label C', 'token' => 'smoke-ss-c']], $badge);
  } catch (PDOException $e) {
    $failed = true;
  }
  check('db_add_site_selection_tokens adds nothing when a token exists', $failed && db_get_existing_site_selection_tokens(['smoke-ss-d']) === []);
  check('db_count_site_selection_tokens searches label and token, ignoring case', call('db_count_site_selection_tokens', 'SMOKE-LABEL') === 3 && db_count_site_selection_tokens('smoke-ss-c') === 1);
  check('db_count_site_selection_tokens escapes wildcards', db_count_site_selection_tokens('smoke_ss') === 0);
  $listed = call('db_get_site_selection_tokens', 2, 1, 'smoke-label', 'label', false);
  check('db_get_site_selection_tokens sorts and pages', array_column($listed, 'label') === ['smoke-label B', 'smoke-label C']
    && $listed[0]['added_by'] === $badge && $listed[0]['added_by_name'] === 'Smoke Tester' && is_int($listed[0]['added_at']) && is_int($listed[0]['id']));
  check('db_get_site_selection_tokens ignores an unknown sort', count(db_get_site_selection_tokens(10, 0, 'smoke-label', 'nope; DROP TABLE members')) === 3);

  check('no site selection at first', call('db_get_member_site_selection', $badge) === null);
  $sold = call('db_sell_online_site_selection_token', $badge);
  $current = db_get_member_site_selection($badge);
  check('db_sell_online_site_selection_token', $sold['success'] === true && $current['token'] === $sold['token'] && $current['method'] === 'online' && $current['sold_by'] === null && is_int($current['sold_at']));
  check('db_sell_online_site_selection_token refuses a second key', db_sell_online_site_selection_token($badge) === ['success' => false, 'error' => 'already_has_token']);
  check('db_sell_paper_site_selection_ballot refuses a member with a key', call('db_sell_paper_site_selection_ballot', $badge, $other_badge) === ['success' => false, 'error' => 'already_has_token']);

  $replaced = call('db_replace_site_selection_token', $badge, $other_badge);
  $current = db_get_member_site_selection($badge);
  check('db_replace_site_selection_token', $replaced['success'] === true && $replaced['token'] !== $sold['token'] && $current['token'] === $replaced['token'] && $current['method'] === 'online' && $current['sold_by'] === $other_badge);
  check('db_count_spoilt_site_selection_tokens', call('db_count_spoilt_site_selection_tokens') >= 1);
  check('db_get_spoilt_site_selection_tokens', in_array($sold['token'], array_column(call('db_get_spoilt_site_selection_tokens', 1000, 0), 'token'), true));

  check('db_transfer_site_selection_to_paper', call('db_transfer_site_selection_to_paper', $badge, $other_badge) === ['success' => true]);
  $current = db_get_member_site_selection($badge);
  check('a transferred member has a paper ballot', $current['method'] === 'paper' && $current['token'] === null && $current['sold_by'] === $other_badge);
  check('db_transfer_site_selection_to_paper refuses a paper ballot', db_transfer_site_selection_to_paper($badge, $other_badge) === ['success' => false, 'error' => 'no_online_token']);
  check('db_replace_site_selection_token refuses a paper ballot', db_replace_site_selection_token($badge, $other_badge) === ['success' => false, 'error' => 'no_online_token']);
  $selections = call('db_get_members_site_selections', [$badge, $other_badge]);
  check('db_get_members_site_selections', array_keys($selections) === [$badge] && $selections[$badge]['method'] === 'paper' && db_get_members_site_selections([]) === []);

  call('db_unassign_site_selection_token', $current['id']);
  check('unassigning a paper ballot deletes it', db_get_member_site_selection($badge) === null && db_fetch_value("SELECT COUNT(*) FROM site_selection_tokens WHERE id = ?", [$current['id']]) == 0);
  check('db_sell_paper_site_selection_ballot', db_sell_paper_site_selection_ballot($other_badge, $badge) === ['success' => true] && db_get_member_site_selection($other_badge)['method'] === 'paper');

  $spoilt_id = db_fetch_value("SELECT id FROM site_selection_tokens WHERE token = ?", [$sold['token']]);
  check('db_delete_site_selection_token refuses an assigned key', call('db_delete_site_selection_token', $spoilt_id) === false);
  db_unassign_site_selection_token($spoilt_id);
  $returned = db_fetch_row("SELECT badge_no, method, spoilt_at FROM site_selection_tokens WHERE id = ?", [$spoilt_id]);
  check('unassigning a key returns it to the pool', $returned === ['badge_no' => null, 'method' => null, 'spoilt_at' => null]);
  check('db_delete_site_selection_token', db_delete_site_selection_token($spoilt_id) === true && db_get_existing_site_selection_tokens([$sold['token']]) === []);

  $sold = db_sell_online_site_selection_token($badge);
  check('db_sell_online_site_selection_token after unassigning', $sold['success'] === true);
  if ($pool_before === 0) {
    check('db_replace_site_selection_token with an empty pool', db_replace_site_selection_token($badge, $other_badge) === ['success' => false, 'error' => 'no_tokens'] && db_get_member_site_selection($badge)['token'] === $sold['token']);
    db_unassign_site_selection_token(db_get_member_site_selection($other_badge)['id']);
    check('db_sell_online_site_selection_token with an empty pool', db_sell_online_site_selection_token($other_badge) === ['success' => false, 'error' => 'no_tokens']);
  } else {
    echo "skip no_tokens checks, as the pool has other keys
";
  }

  // Payment links and details
  call('db_record_site_selection_payment_link', 'smoke-link-1', $badge, 'smoke-order-1');
  call('db_record_site_selection_payment_link', 'smoke-link-1', $badge, 'smoke-order-1');
  check('db_get_latest_site_selection_payment_link', call('db_get_latest_site_selection_payment_link', $badge) === 'smoke-link-1' && db_get_latest_site_selection_payment_link($other_badge) === null);
  call('db_record_site_selection_payment_link', 'smoke-link-2', $badge, null);
  call('db_mark_site_selection_payment_link_paid', $badge, 'smoke-order-1');
  check('db_mark_site_selection_payment_link_paid by order', db_fetch_value("SELECT COUNT(*) FROM site_selection_payment_links WHERE badge_no = ? AND paid_at IS NOT NULL", [$badge]) == 1);
  db_mark_site_selection_payment_link_paid($badge, 'smoke-order-2');
  check('db_mark_site_selection_payment_link_paid falls back to the latest unpaid link', db_fetch_value("SELECT order_id FROM site_selection_payment_links WHERE payment_link_id = 'smoke-link-2' AND paid_at IS NOT NULL") === 'smoke-order-2');

  check('no site selection info at first', call('db_get_site_selection_info', $badge) === null);
  call('db_set_site_selection_info', $badge, 'GB', "1 Smoke St\n=Town", true, false);
  check('db_set_site_selection_info', db_get_site_selection_info($badge) === ['country' => 'GB', 'address' => "1 Smoke St\n=Town", 'wants_paper_publications' => true, 'consent_to_publish_name' => false]);
  db_set_site_selection_info($badge, 'US', 'Elsewhere', false, true);
  check('db_set_site_selection_info updates', db_get_site_selection_info($badge) === ['country' => 'US', 'address' => 'Elsewhere', 'wants_paper_publications' => false, 'consent_to_publish_name' => true]);

  $export = array_column(call('db_get_site_selection_export'), null, 'badge_no');
  check('db_get_site_selection_export', isset($export[$badge]) && !isset($export[$other_badge])
    && $export[$badge]['real_name'] === 'Smoke Realer' && $export[$badge]['country'] === 'US' && $export[$badge]['consent_to_publish_name'] === true);

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
  'db_format_discord_post', 'db_signup_values', 'db_transaction',
  'db_site_selection_token_where', 'db_site_selection_info_row', 'db_lock_member_site_selection',
  'db_take_site_selection_token', 'db_assign_site_selection_token', 'db_spoil_site_selection_token',
  'db_insert_paper_site_selection_ballot',
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
