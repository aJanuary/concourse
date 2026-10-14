<?php

/*
 * Programme handlers
 *
 * A handler decides how a member gets into a programme item: which platform
 * they are sent to and what they see on the way. The deep-link pages
 * (/deep-link/session, /participate, /replay and /stream) never know about
 * platforms. They check that the member may join and then ask the item's
 * handler what to do.
 *
 * Handlers are chosen per session by prog_sessions.handler, falling back to
 * DEFAULT_PROG_HANDLER. Replays are opened by PROG_REPLAY_HANDLER. Only names
 * in AVAILABLE_PROG_HANDLERS can be chosen in the admin pages and the
 * sessions API.
 *
 * To add a handler:
 *  1. Implement ProgHandler in lib/prog_handlers/.
 *  2. Add it to prog_handler_factories() below.
 *  3. Add its name to AVAILABLE_PROG_HANDLERS in config.php.
 *
 * join() is given an item:
 *
 *   kind         'session' or 'replay'
 *   item_id      the programme item id (the room id for the stream deep link)
 *   title        the item's title, possibly null
 *   destination  the handler-specific value stored with the item: a platform
 *                session id, a room id, a replay URL
 *   member       ['badge_no', 'name', 'email'], or null when nobody is logged
 *                in (only possible for replays, see PROG_REPLAY_REQUIRES_LOGIN)
 *
 * and returns a ProgHandlerResult: redirect, message, embed or
 * login_required. The deep-link page sends it to the browser.
 */

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');

class ProgHandlerResult {
  private function __construct(
    public readonly string $type,
    public readonly ?string $url = null,
    public readonly ?string $heading = null,
    public readonly ?string $text = null
  ) {}

  public static function redirect(string $url): self {
    return new self('redirect', $url);
  }

  // A page with a heading and a paragraph of plain text
  public static function message(string $heading, string $text): self {
    return new self('message', null, $heading, $text);
  }

  // A full page player for $url, which must be an embeddable address
  public static function embed(string $url, ?string $title = null): self {
    return new self('embed', $url, $title);
  }

  public static function login_required(): self {
    return new self('login');
  }
}

interface ProgHandler {
  public function join(array $item): ProgHandlerResult;
}

// Constructors are deferred so a handler's dependencies are only loaded when it is used
function prog_handler_factories() {
  return [
    'zoomevents' => function () {
      require_once(__DIR__ . '/prog_handlers/ZoomEventsHandler.php');
      return new ZoomEventsHandler();
    },
    'stream' => function () {
      require_once(__DIR__ . '/prog_handlers/StreamHandler.php');
      return new StreamHandler();
    },
    'discord' => function () {
      require_once(__DIR__ . '/prog_handlers/DiscordHandler.php');
      return new DiscordHandler();
    },
  ];
}

function prog_get_handler($name) {
  $factories = prog_handler_factories();
  if (!in_array($name, AVAILABLE_PROG_HANDLERS, true) || !isset($factories[$name])) {
    throw new RuntimeException("Unknown programme handler '$name'");
  }
  return $factories[$name]();
}

function prog_render_message($title, $heading, $text, $breadcrumb) {
  render_header($title, $heading . '.', ['Home' => '/', $breadcrumb]);
  ?>
  <article>
    <h2><?php echo htmlspecialchars($heading); ?></h2>
    <p><?php echo htmlspecialchars($text); ?></p>
  </article>
  <?php
  render_footer();
  exit;
}

function prog_render_login($title, $breadcrumb) {
  render_header($title, 'Log in to continue.', ['Home' => '/', $breadcrumb]);
  ?>
  <article>
    <h2>Log in to continue</h2>
    <p>You need to be logged in to do this. Please log in to the members portal to continue.</p>
    <p><a href="<?php echo htmlspecialchars(make_login_link()); ?>" class="button">Log in</a></p>
  </article>
  <?php
  render_footer();
  exit;
}

function prog_render_embed($url, $title) {
  ?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title><?php echo htmlspecialchars($title ?? 'Replay'); ?></title><style>
  html, body { margin: 0; height: 100%; background: #000; }
  iframe { display: block; width: 100%; height: 100%; border: none; }
</style></head>
<body>
  <iframe
    src="<?php echo htmlspecialchars($url); ?>"
    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
    allowfullscreen
  ></iframe>
</body>
</html>
  <?php
  exit;
}

function prog_send_result(ProgHandlerResult $result, $title, $breadcrumb) {
  switch ($result->type) {
    case 'redirect':
      header('Location: ' . $result->url);
      exit;
    case 'message':
      prog_render_message($title, $result->heading, $result->text, $breadcrumb);
    case 'embed':
      prog_render_embed($result->url, $result->heading);
    case 'login':
      prog_render_login($title, $breadcrumb);
  }
  throw new LogicException("Unknown programme handler result '{$result->type}'");
}

function prog_current_member() {
  $badge_no = get_current_user_badge_no();
  if ($badge_no === null) {
    return null;
  }
  return ['badge_no' => $badge_no] + db_get_member($badge_no);
}

// Shows the "you're early" page when PROG_TEST_MODE is on
function prog_check_test_mode($title, $breadcrumb) {
  if (PROG_TEST_MODE) {
    prog_render_message(
      $title,
      "You're early!",
      "You've gotten to the right place. When we're live, you'll end up at a page where you can join.",
      $breadcrumb
    );
  }
}

// Shows a message and exits unless the member may join
function prog_check_may_join($title, $breadcrumb) {
  if (PROG_JOIN_PERMISSION !== '' && !current_user_has_permission(PROG_JOIN_PERMISSION)) {
    prog_render_message(
      $title,
      'Not available',
      "Your membership doesn't include access to this. Please contact " . SUPPORT_EMAIL . ' if you think that is a mistake.',
      $breadcrumb
    );
  }
}

// Sends the member to a session through its handler. Does not return.
function prog_join_session($item_id, $title, $breadcrumb) {
  prog_check_test_mode($title, $breadcrumb);
  if (!is_logged_in()) {
    prog_render_login($title, $breadcrumb);
  }
  prog_check_may_join($title, $breadcrumb);

  $session = db_get_prog_session($item_id);
  if (!$session['found']) {
    prog_render_message($title, 'Unknown item', "Sorry, we don't know about that item.", $breadcrumb);
  }

  $handler = prog_get_handler($session['handler'] ?? DEFAULT_PROG_HANDLER);
  $result = $handler->join([
    'kind' => 'session',
    'item_id' => $item_id,
    'title' => $session['title'],
    'destination' => $session['destination'],
    'member' => prog_current_member(),
  ]);
  prog_send_result($result, $title, $breadcrumb);
}

// Sends the member to a room's live stream. Does not return.
function prog_join_room_stream($room_id, $title, $breadcrumb) {
  prog_check_test_mode($title, $breadcrumb);
  if (!is_logged_in()) {
    prog_render_login($title, $breadcrumb);
  }
  prog_check_may_join($title, $breadcrumb);

  $result = prog_get_handler('stream')->join([
    'kind' => 'session',
    'item_id' => $room_id,
    'title' => null,
    'destination' => $room_id,
    'member' => prog_current_member(),
  ]);
  prog_send_result($result, $title, $breadcrumb);
}

// Sends the member to a replay through the replay handler. Does not return.
function prog_open_replay($item_id, $title, $breadcrumb) {
  if (PROG_REPLAY_REQUIRES_LOGIN && !is_logged_in()) {
    prog_render_login($title, $breadcrumb);
  }
  if (is_logged_in()) {
    prog_check_may_join($title, $breadcrumb);
  }

  $replay = db_get_replay($item_id);
  if ($replay['unavailable']) {
    prog_render_message(
      $title,
      'Replay unavailable',
      'Unfortunately there was an issue recording this item, so no replay will be available for it.',
      $breadcrumb
    );
  }
  if (!$replay['replay_url']) {
    prog_render_message(
      $title,
      'No replay available yet',
      "Replay isn't available for this item yet. We're working to get replays up as soon as we can, so check back soon.",
      $breadcrumb
    );
  }

  $result = prog_get_handler(PROG_REPLAY_HANDLER)->join([
    'kind' => 'replay',
    'item_id' => $item_id,
    'title' => $replay['title'],
    'destination' => $replay['replay_url'],
    'member' => prog_current_member(),
  ]);
  prog_send_result($result, $title, $breadcrumb);
}
