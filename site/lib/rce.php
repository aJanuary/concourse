<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

function get_magic_rce_link($session) {
  $magic_link = db_get_magic_link($session);
  error_log("++++MAGIC LINK IS " . $magic_link);
  if ($magic_link) {
    return $magic_link;
  }

  $badge_no = get_current_user_badge_no();
  error_log("++++BADGE NO IS " . $badge_no);
  $member = db_get_member($badge_no);

  $magic_link_resp = api_call('https://api.events.ringcentral.com/v1/tickets/' . RCE_TICKET_ID . '/magicLinks', [
    'Authorization: Bearer ' . RCE_API_KEY,
    'Content-Type: application/json'
  ], json_encode([
    'data' => [
      'type' => 'magicLink',
      'attributes' => [
        'email' => $member['email'],
        'firstName' => $member['name'],
        'headline' => $badge_no,
        'lastName' => '-',
        'registrationId' => $badge_no
      ]
    ]
  ]));
  $magic_link = $magic_link_resp['data']['attributes']['magicLink'];
  db_create_invite($badge_no, $magic_link);
  return $magic_link;
}
?>