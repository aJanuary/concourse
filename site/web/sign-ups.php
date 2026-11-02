<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/signups.php');

$signups = current_user_has_permission('see-signup-links') ? signups_sort(db_get_signups()) : [];

// Categories, each a list of groups of sign-ups. A group starts at each new
// session time, and has no time when the sign-ups have none.
$categories = [];
$category_links = [];
if ($signups) {
  [$category_anchors, $signup_anchors] = signup_anchors($signups);
  $member = is_logged_in() ? [
    'name' => get_current_user_name(),
    'email' => get_current_user_email(),
    'badge_no' => get_current_user_badge_no(),
  ] : null;
  $now = time();

  foreach ($category_anchors as $category => $anchor) {
    $category_links[] = ['name' => $category, 'anchor' => $anchor];
  }

  $current_category = null;
  $current_start = null;
  foreach ($signups as $signup) {
    $new_category = $signup['category'] !== $current_category;
    $new_start = $signup['session_start'] !== null && ($new_category || $signup['session_start'] !== $current_start);
    if ($new_category) {
      $current_category = $signup['category'];
      $current_start = null;
      $categories[] = ['name' => $current_category, 'anchor' => $category_anchors[$current_category], 'groups' => []];
    }
    $category = &$categories[count($categories) - 1];
    if ($new_start) {
      $current_start = $signup['session_start'];
    }
    if ($new_category || $new_start) {
      $category['groups'][] = [
        'start' => $new_start ? signup_format_time($current_start, 'l j F, H:i') : null,
        'signups' => [],
      ];
    }

    $state = signup_state($signup, $now);
    $category['groups'][count($category['groups']) - 1]['signups'][] = [
      'title' => $signup['title'],
      'anchor' => $signup_anchors[$signup['id']],
      'notes' => $signup['notes'],
      'guide_url' => $signup['guide_url'],
      'state' => $state,
      'opens' => $state === 'not-open' ? signup_format_time($signup['opens_at'], 'l j F Y \a\t H:i') : null,
      'closes' => $state === 'open' && $signup['closes_at'] !== null ? signup_format_time($signup['closes_at'], 'l j F Y \a\t H:i') : null,
      'url' => $state === 'open' ? signup_member_url($signup['url'], $member) : null,
    ];
    unset($category);
  }
}

render_page('pages/sign-ups.html.twig', [
  'title' => 'Sign-ups',
  'description' => 'Sign-up for programme items at ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Sign-ups'],
  'category_links' => $category_links,
  'categories' => $categories,
]);
