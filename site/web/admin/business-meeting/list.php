<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/business_meeting.php');

check_permission('manage-business-meeting');

$messages = [
  'saved' => 'Saved the link for badge %s.',
  'deleted' => 'Deleted the link for badge %s.',
];
$message = isset($messages[$_GET['done'] ?? '']) ? sprintf($messages[$_GET['done']], (string) ($_GET['badge_no'] ?? '')) : null;

$links = db_get_business_meeting_links();

$rows = [];
foreach ($links as $link) {
  $rows[] = [
    'badge_no' => $link['badge_no'],
    'name' => $link['name'] ?? '',
    'join_link' => $link['join_link'],
    'updated' => business_meeting_format_time((new DateTime('@' . $link['updated_at']))->setTimezone(new DateTimeZone(TIMEZONE))),
  ];
}

render_page('pages/admin/business-meeting/list.html.twig', [
  'message' => $message,
  'platform' => BUSINESS_MEETING['platform_name'],
  'rows' => $rows,
]);
