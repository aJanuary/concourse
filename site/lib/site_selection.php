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

const SITE_SELECTION_TOKEN_MAX_LENGTH = 100;
const SITE_SELECTION_LABEL_MAX_LENGTH = 255;

/**
 * Reads voter keys pasted as CSV rows of label,token. Returns [entries,
 * error]: entries is a list of ['label' => ..., 'token' => ...], and error
 * describes the first bad row, or is null. Blank lines are skipped.
 */
function site_selection_tokens_from_csv($text) {
  $entries = [];
  $seen = [];
  $row_no = 0;
  foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {
    $row_no++;
    if (trim($line) === '') {
      continue;
    }
    $cells = array_map('trim', str_getcsv($line, ',', '"', ''));
    $label = $cells[0] ?? '';
    $token = $cells[1] ?? '';
    if (count($cells) !== 2 || $label === '' || $token === '') {
      return [[], "Row $row_no doesn't have a label and a token, like \"Batch A,abc-def-ghi\"."];
    }
    if (strlen($token) > SITE_SELECTION_TOKEN_MAX_LENGTH) {
      return [[], "The token in row $row_no is longer than " . SITE_SELECTION_TOKEN_MAX_LENGTH . ' characters.'];
    }
    if (strlen($label) > SITE_SELECTION_LABEL_MAX_LENGTH) {
      return [[], "The label in row $row_no is longer than " . SITE_SELECTION_LABEL_MAX_LENGTH . ' characters.'];
    }
    if (isset($seen[$token])) {
      return [[], "Row $row_no has the same token as row {$seen[$token]}."];
    }
    $seen[$token] = $row_no;
    $entries[] = ['label' => $label, 'token' => $token];
  }
  if (!$entries) {
    return [[], 'Enter at least one row.'];
  }
  return [$entries, null];
}

function site_selection_format_time($timestamp) {
  return (new DateTime('@' . $timestamp))->setTimezone(new DateTimeZone(TIMEZONE))->format('Y-m-d H:i');
}
