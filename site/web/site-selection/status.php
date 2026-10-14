<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

// Polled by the processing page with the member's session cookie, so unlike
// /api/* it isn't for other servers. {"has_token": true} once the member can
// vote, online or on paper.
check_permission('see-site-selection');

header('Content-Type: application/json');
header('Cache-Control: no-store');
echo json_encode(['has_token' => db_get_member_site_selection(get_current_user_badge_no()) !== null]);
