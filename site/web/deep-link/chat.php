<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

if (array_key_exists('check_has_joined_discord', $_GET)) {
  $usernames = db_get_discord_usernames($_COOKIE[SESSION_COOKIE_NAME]);
  echo !empty($usernames) ? 'true' : 'false';
  exit;
}

// $outcome is the part of the template to show: not-open, unknown-item,
// nothing-scheduled or join
function render_chat_page($outcome, $description, $context = []): never {
  render_page('pages/deep-link/chat.html.twig', [
    'description' => $description,
    'outcome' => $outcome,
  ] + $context);
  exit;
}

if (!current_user_has_permission('see-discord-posts')) {
  render_chat_page('not-open', 'Discord chat for programme is not yet open.');
}

if (array_key_exists('item_id', $_GET)) {
  $item_id = $_GET['item_id'];
  $post = db_get_discord_post($item_id);

  if (!$post['post_url']) {
    $room_id = $_GET['room_id'];
    $post = db_get_discord_post($room_id);
    if (!$post['post_url']) {
      render_chat_page('unknown-item', 'Unknown item in the ' . CON_NAME . ' Discord.');
    }
  }
} else if (array_key_exists('room_id', $_GET)) {
  $room_id = $_GET['room_id'];
  $post = db_get_discord_post_by_room_and_time($room_id, time());

  if (!$post['post_url']) {
    render_chat_page('nothing-scheduled', 'Nothing scheduled in that room in the ' . CON_NAME . ' Discord.');
  }
} else {
  die('Missing required query parameter "item_id" or "room_id"');
}

$usernames = db_get_discord_usernames($_COOKIE[SESSION_COOKIE_NAME]);
if (!empty($usernames)) {
  header('Location: ' . $post['post_url']);
  exit;
}

render_chat_page('join', 'Join the ' . CON_NAME . ' Discord server.', [
  'logged_in' => is_logged_in(),
  'invite_url' => discord_authorize_url(),
  'post_url' => $post['post_url'],
]);
