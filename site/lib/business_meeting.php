<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

const BUSINESS_MEETING_BADGE_MAX_LENGTH = 36;
const BUSINESS_MEETING_LINK_MAX_LENGTH = 2048;

// A 'yyyy-mm-dd hh:mm' from BUSINESS_MEETING in the convention's time zone
function business_meeting_time($value) {
  $date = DateTime::createFromFormat('!Y-m-d H:i', trim($value), new DateTimeZone(TIMEZONE));
  if ($date === false) {
    throw new RuntimeException("BUSINESS_MEETING has a time '$value' that isn't yyyy-mm-dd hh:mm");
  }
  return $date;
}

function business_meeting_format_time($date) {
  return $date->format('l j F Y, H:i T');
}

// The Mark Protection Committee election from BUSINESS_MEETING, or null if
// there isn't one
/** @return array{url: string, opens: string, closes: string}|null */
function business_meeting_mpc_election() {
  return BUSINESS_MEETING['mpc_election'] ?? null;
}

// 'before', 'open' or 'closed', or null if there is no election
function business_meeting_mpc_state($now) {
  $election = business_meeting_mpc_election();
  if (!$election) {
    return null;
  }
  if ($now < business_meeting_time($election['opens'])) {
    return 'before';
  }
  if ($now < business_meeting_time($election['closes'])) {
    return 'open';
  }
  return 'closed';
}

// Null if the badge number is fine
function business_meeting_badge_error($badge_no) {
  if ($badge_no === '') {
    return 'The badge number is missing';
  }
  if (strlen($badge_no) > BUSINESS_MEETING_BADGE_MAX_LENGTH) {
    return 'The badge number is longer than ' . BUSINESS_MEETING_BADGE_MAX_LENGTH . ' characters';
  }
  return null;
}

// Null if the link is fine. Doesn't include the link, which is a credential.
function business_meeting_link_error($join_link) {
  if ($join_link === '') {
    return 'The join link is missing';
  }
  if (strlen($join_link) > BUSINESS_MEETING_LINK_MAX_LENGTH) {
    return 'The join link is longer than ' . BUSINESS_MEETING_LINK_MAX_LENGTH . ' characters';
  }
  if (filter_var($join_link, FILTER_VALIDATE_URL) === false || strtolower((string) parse_url($join_link, PHP_URL_SCHEME)) !== 'https') {
    return 'The join link is not an https:// URL';
  }
  return null;
}

/**
 * Reads join links from CSV text with a header row. The columns are found by
 * the names badge_no and join_link, or are the first two columns if the
 * header doesn't name them. Returns [links, problems]: links maps badge
 * numbers to join links, with later rows replacing earlier ones, and problems
 * lists rows that were skipped or replaced. Rows are numbered as in a
 * spreadsheet, so the header is row 1.
 */
function business_meeting_links_from_csv($text) {
  // Spreadsheets often start UTF-8 files with a byte order mark
  if (strncmp($text, "\xEF\xBB\xBF", 3) === 0) {
    $text = substr($text, 3);
  }

  // fgetcsv rather than splitting on newlines, so quoted cells can span lines
  $stream = fopen('php://temp', 'r+');
  fwrite($stream, $text);
  rewind($stream);
  $header = null;
  $links = [];
  $first_rows = [];
  $problems = [];
  $row_no = 0;
  while (($cells = fgetcsv($stream, null, ',', '"', '')) !== false) {
    $row_no++;
    $cells = array_map(fn($cell) => trim((string) $cell), $cells);
    if ($header === null) {
      $header = array_map('strtolower', $cells);
      $badge_col = array_search('badge_no', $header, true);
      $link_col = array_search('join_link', $header, true);
      if ($badge_col === false || $link_col === false) {
        [$badge_col, $link_col] = [0, 1];
      }
      continue;
    }
    // fgetcsv gives [null] for a blank line
    if (implode('', $cells) === '') {
      continue;
    }

    $badge_no = $cells[$badge_col] ?? '';
    $join_link = $cells[$link_col] ?? '';
    $error = business_meeting_badge_error($badge_no) ?? business_meeting_link_error($join_link);
    if ($error !== null) {
      $problems[] = "Row $row_no skipped: " . lcfirst($error);
      continue;
    }
    if (isset($first_rows[$badge_no])) {
      $problems[] = "Row $row_no replaces the link for badge $badge_no from row {$first_rows[$badge_no]}";
    } else {
      $first_rows[$badge_no] = $row_no;
    }
    $links[$badge_no] = $join_link;
  }
  fclose($stream);

  if ($header === null) {
    $problems[] = 'The file is empty';
  }
  return [$links, $problems];
}
