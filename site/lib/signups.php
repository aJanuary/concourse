<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');

const SIGNUP_FIELDS = ['title', 'category', 'url', 'guide_url', 'notes', 'session_start', 'opens_at', 'closes_at'];
const SIGNUP_REQUIRED_FIELDS = ['title', 'category', 'url'];
const SIGNUP_TIME_FIELDS = ['session_start', 'opens_at', 'closes_at'];
const SIGNUP_URL_FIELDS = ['url', 'guide_url'];

// Unix timestamp for a 'yyyy-mm-dd hh:mm' in the convention's time zone. Also
// accepts the 'T' separator that datetime-local inputs send. Null if malformed.
function signup_parse_time($value) {
  $value = str_replace('T', ' ', trim($value));
  $date = DateTime::createFromFormat('!Y-m-d H:i', $value, new DateTimeZone(TIMEZONE));
  if ($date === false || $date->format('Y-m-d H:i') !== $value) {
    return null;
  }
  return $date->getTimestamp();
}

function signup_format_time($timestamp, $format = 'Y-m-d H:i') {
  if ($timestamp === null) {
    return '';
  }
  return (new DateTime('@' . $timestamp))->setTimezone(new DateTimeZone(TIMEZONE))->format($format);
}

/**
 * Validates submitted fields (strings, as from a form or CSV row) and converts
 * them to a signup ready for the database. Returns [signup, errors]; the
 * signup is null when there are errors.
 */
function signup_from_input($input) {
  $signup = [];
  $errors = [];

  foreach (SIGNUP_FIELDS as $field) {
    $value = trim((string) ($input[$field] ?? ''));
    $signup[$field] = $value === '' ? null : $value;
  }

  foreach (SIGNUP_REQUIRED_FIELDS as $field) {
    if ($signup[$field] === null) {
      $errors[] = "$field is required";
    }
  }

  // The links are rendered as hrefs, so anything else (javascript: and so on) is refused
  foreach (SIGNUP_URL_FIELDS as $field) {
    if ($signup[$field] !== null && !preg_match('#^https?://[^\s]+$#i', $signup[$field])) {
      $errors[] = "$field must be an http or https URL";
    }
  }

  foreach (SIGNUP_TIME_FIELDS as $field) {
    if ($signup[$field] !== null) {
      $timestamp = signup_parse_time($signup[$field]);
      if ($timestamp === null) {
        $errors[] = "$field must be in the format yyyy-mm-dd hh:mm";
      }
      $signup[$field] = $timestamp;
    }
  }

  if ($signup['opens_at'] !== null && $signup['closes_at'] !== null && $signup['closes_at'] <= $signup['opens_at']) {
    $errors[] = 'closes_at must be after opens_at';
  }

  if (mb_strlen($signup['title'] ?? '') > 256) {
    $errors[] = 'title must be at most 256 characters';
  }
  if (mb_strlen($signup['category'] ?? '') > 100) {
    $errors[] = 'category must be at most 100 characters';
  }
  foreach (SIGNUP_URL_FIELDS as $field) {
    if (strlen($signup[$field] ?? '') > 2048) {
      $errors[] = "$field must be at most 2048 characters";
    }
  }

  return [$errors ? null : $signup, $errors];
}

/**
 * Parses pasted CSV with a header row naming the columns. Returns
 * [signups, errors]; signups is empty when there are any errors so that a
 * bad row never leads to a partial import.
 */
function signups_from_csv($text) {
  // fgetcsv rather than splitting on newlines, so quoted cells can span lines
  $stream = fopen('php://temp', 'r+');
  fwrite($stream, trim($text));
  rewind($stream);
  $rows = [];
  while (($row = fgetcsv($stream, null, ',', '"', '')) !== false) {
    $rows[] = $row;
  }
  fclose($stream);

  if (!$rows || $rows[0] === [null]) {
    return [[], ['No rows to import']];
  }

  $header = array_map(fn($col) => strtolower(trim((string) $col)), array_shift($rows));
  $unknown = array_diff($header, SIGNUP_FIELDS);
  if ($unknown) {
    return [[], ['Unknown columns in header: ' . implode(', ', $unknown)]];
  }
  $missing = array_diff(SIGNUP_REQUIRED_FIELDS, $header);
  if ($missing) {
    return [[], ['Missing required columns in header: ' . implode(', ', $missing)]];
  }
  if (count(array_unique($header)) !== count($header)) {
    return [[], ['Header names a column more than once']];
  }

  $signups = [];
  $errors = [];
  foreach ($rows as $index => $cells) {
    $row_no = $index + 1;
    // fgetcsv gives [null] for a blank line
    if ($cells === [null]) {
      continue;
    }
    if (count($cells) !== count($header)) {
      $errors[] = "Row $row_no: expected " . count($header) . ' columns but found ' . count($cells);
      continue;
    }
    [$signup, $row_errors] = signup_from_input(array_combine($header, $cells));
    foreach ($row_errors as $error) {
      $errors[] = "Row $row_no: $error";
    }
    if ($signup !== null) {
      $signups[] = $signup;
    }
  }

  if (!$errors && !$signups) {
    $errors[] = 'No rows to import';
  }

  return [$errors ? [] : $signups, $errors];
}

function signup_state($signup, $now) {
  if ($signup['opens_at'] !== null && $now < $signup['opens_at']) {
    return 'not-open';
  }
  if ($signup['closes_at'] !== null && $now >= $signup['closes_at']) {
    return 'closed';
  }
  return 'open';
}

function signup_placeholders($name, $email, $badge_no) {
  return [
    '{name}' => rawurlencode($name ?? ''),
    '{email}' => rawurlencode($email ?? ''),
    '{badge_no}' => rawurlencode($badge_no ?? ''),
  ];
}

/**
 * Fills in {name}, {email} and {badge_no} anywhere in a signup URL. With no
 * member (anonymous), query parameters holding a placeholder are dropped and
 * placeholders elsewhere are removed, rather than filled in.
 */
function signup_member_url($url, $member) {
  if ($member !== null) {
    // strtr makes one pass, so a member's name containing {email} is not replaced again
    return strtr($url, signup_placeholders($member['name'], $member['email'], $member['badge_no']));
  }

  $placeholders = signup_placeholders(null, null, null);
  if (!preg_match('/^([^?#]*)(?:\?([^#]*))?(#.*)?$/', $url, $matches)) {
    return strtr($url, $placeholders);
  }
  $base = $matches[1];
  $query = $matches[2] ?? '';
  $fragment = $matches[3] ?? '';

  $params = [];
  if ($query !== '') {
    foreach (explode('&', $query) as $param) {
      if (strtr($param, $placeholders) === $param) {
        $params[] = $param;
      }
    }
  }

  return strtr($base, $placeholders) . ($params ? '?' . implode('&', $params) : '') . strtr($fragment, $placeholders);
}

// Categories, then start time (entries with no time first), then title
function signups_sort($signups) {
  usort($signups, function ($a, $b) {
    return strcasecmp($a['category'], $b['category'])
      ?: ($a['session_start'] ?? PHP_INT_MIN) <=> ($b['session_start'] ?? PHP_INT_MIN)
      ?: strcasecmp($a['title'], $b['title'])
      ?: $a['id'] <=> $b['id'];
  });
  return $signups;
}

function signup_slug($text) {
  return trim(strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $text)), '-');
}

/**
 * Anchors for each category and signup, generated from their names. Signups
 * with the same title get a numeric suffix so each anchor is unique.
 * Returns [category => anchor] and [signup id => anchor].
 */
function signup_anchors($signups) {
  $used = [];
  $unique = function ($slug) use (&$used) {
    $anchor = $slug;
    $n = 2;
    while (isset($used[$anchor])) {
      $anchor = $slug . '-' . $n++;
    }
    $used[$anchor] = true;
    return $anchor;
  };

  $category_anchors = [];
  foreach ($signups as $signup) {
    if (!isset($category_anchors[$signup['category']])) {
      $category_anchors[$signup['category']] = $unique('category-' . (signup_slug($signup['category']) ?: 'other'));
    }
  }

  $signup_anchors = [];
  foreach ($signups as $signup) {
    $signup_anchors[$signup['id']] = $unique(signup_slug($signup['title']) ?: 'signup');
  }

  return [$category_anchors, $signup_anchors];
}
