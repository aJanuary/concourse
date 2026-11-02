<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

// Each room, with no url when it isn't streaming
$streams = [];
$can_watch = is_logged_in() && (PROG_JOIN_PERMISSION === '' || current_user_has_permission(PROG_JOIN_PERMISSION));
if ($can_watch) {
  foreach (db_get_stream_urls() as $stream_url) {
    $streams[] = [
      'room_id' => $stream_url['room_id'],
      'title' => $stream_url['title'] ?: $stream_url['room_id'],
      'streaming' => (bool) $stream_url['url'],
    ];
  }
}

render_page('pages/stream.html.twig', [
  'title' => 'Stream',
  'description' => 'Watch streams and replays of programme items at ' . CON_NAME . '.',
  'breadcrumbs' => ['Home' => '/', 'Stream and replay'],
  'logged_in' => is_logged_in(),
  'can_watch' => $can_watch,
  'streams' => $streams,
]);
