<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/business_meeting.php');

check_permission('see-business-meeting');

$join_link = db_get_business_meeting_link(get_current_user_badge_no());
$election = business_meeting_mpc_election();
$mpc_state = business_meeting_mpc_state(new DateTime());

render_page('pages/business-meeting.html.twig', [
  'meeting' => BUSINESS_MEETING,
  'join_link' => $join_link,
  'mpc_state' => $mpc_state,
  'mpc_url' => $election['url'] ?? null,
  'mpc_opens' => $mpc_state !== null ? business_meeting_format_time(business_meeting_time($election['opens'])) : null,
  'mpc_closes' => $mpc_state !== null ? business_meeting_format_time(business_meeting_time($election['closes'])) : null,
  'discord_invite' => DISCORD_BM_INVITE,
  'sessions' => array_map(fn($session) => business_meeting_format_time(business_meeting_time($session)), BUSINESS_MEETING['sessions']),
]);
