<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');

check_permission('see-site-selection');

$assignment = db_get_member_site_selection(get_current_user_badge_no());
$just_paid = ($_GET['success'] ?? '') === 'true';
$bids = SITE_SELECTION['bids'];
shuffle($bids);

render_page('pages/site-selection.html.twig', [
  'title' => 'Site selection',
  'description' => 'Vote in site selection for the ' . SITE_SELECTION['year'] . ' Worldcon.',
  'breadcrumbs' => ['Home' => '/', 'Site selection'],
  'site_selection' => SITE_SELECTION,
  'bids' => $bids,
  'assignment' => $assignment,
  'ballot_url' => $assignment !== null && $assignment['method'] === 'online' ? site_selection_ballot_url($assignment['token']) : null,
  'just_paid' => $just_paid,
]);
