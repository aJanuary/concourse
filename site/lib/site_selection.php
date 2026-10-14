<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

// Where a member votes online with their voter key
function site_selection_ballot_url($token) {
  return str_replace('{token}', rawurlencode($token), SITE_SELECTION['ballot_url']);
}

// Called when Square says the member has paid. Returns the result of
// db_sell_online_site_selection_token(); a member who already has a key
// keeps it, so a repeated webhook does nothing.
function site_selection_complete_purchase($badge_no, $order_id) {
  return db_transaction(function () use ($badge_no, $order_id) {
    $result = db_sell_online_site_selection_token($badge_no);
    db_mark_site_selection_payment_link_paid($badge_no, $order_id);
    return $result;
  });
}

// The desk finds members through the member source, so they may not be in
// the members table yet. $member is a row from MemberSource::search().
function site_selection_ensure_member($member) {
  if (!db_member_exists($member['badge_no'])) {
    db_create_member($member['badge_no'], $member['email'], $member['name']);
  }
}
