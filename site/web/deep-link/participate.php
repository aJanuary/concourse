<?php
// Deliberately don't allow implicit anonymous access.
// This can redirect straight to the zoom URL and the stream yard URLs.
// While we have people in those spaces to make sure the right people are there,
// if we allow anonymous access there is potential for people to join en masse
// and cause disruption.
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/prog_handlers.php');

$title = 'Participate in an item';

if (array_key_exists('item_id', $_GET)) {
  prog_join_session($_GET['item_id'], $title, $title);
} else if (!array_key_exists('room_id', $_GET)) {
  die('Missing required query parameter "item_id" or "room_id"');
}

prog_check_test_mode($title, $title);
if (!is_logged_in()) {
  prog_render_login($title, $title);
}
prog_check_may_join($title, $title);

$stage = db_get_stage($_GET['room_id']);
if ($stage['type'] == 'hybrid') {
  $participant_url = db_get_zoom_url();
} else {
  $participant_url = $stage['participant_url'];
}

if (!$participant_url) {
  prog_render_message(
    $title,
    'Unknown item',
    "Oops, something went wrong and we don't know where you should be going. Please get in touch with the programme team to find out.",
    $title
  );
}

header('Location: ' . $participant_url);
exit;
