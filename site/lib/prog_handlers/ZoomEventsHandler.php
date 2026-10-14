<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');

// Sends members into a session of a Zoom Events event. A session's
// destination, and a replay's, is the Zoom Events session id.
//
// Each member gets one ticket for the event, created through the API the
// first time they join. Its join link signs them in, so they can't be sent
// to a session without being logged in to the portal first.
class ZoomEventsHandler implements ProgHandler {
  // Zoom's code for an event that exists but isn't published yet
  private const EVENT_NOT_PUBLISHED = 26501;

  // Tokens are refreshed a little early so one never expires mid-request
  private const TOKEN_EXPIRY_MARGIN = 60;

  public function join(array $item): ProgHandlerResult {
    if ($item['member'] === null) {
      return ProgHandlerResult::login_required();
    }

    $join_link = $this->get_or_create_join_link($item['member']);
    if ($join_link === null) {
      return ProgHandlerResult::message('Not open yet', 'The event has not opened yet. Please check back later.');
    }
    return ProgHandlerResult::redirect($join_link . '/sessions/' . rawurlencode($item['destination']));
  }

  private function get_or_create_join_link($member) {
    $ticket = db_get_zoom_events_ticket($member['badge_no']);
    if ($ticket) {
      return $ticket['event_join_link'];
    }

    require_config('zoom_events', 'event_id', 'ticket_type_id');
    if (ZOOM_EVENTS_TICKET_EMAIL_DOMAIN !== '') {
      $email = 'zoomlogin+' . $member['badge_no'] . '@' . ZOOM_EVENTS_TICKET_EMAIL_DOMAIN;
    } else {
      $email = $member['email'];
    }

    $response = api_call('https://api.zoom.us/v2/zoom_events/events/' . rawurlencode(ZOOM_EVENTS_EVENT_ID) . '/tickets', [
      'Authorization: Bearer ' . $this->auth_token(),
      'Content-Type: application/json',
    ], json_encode([
      'tickets' => [[
        'email' => $email,
        'ticket_type_id' => ZOOM_EVENTS_TICKET_TYPE_ID,
        'send_notification' => false,
        'fast_join' => true,
        'first_name' => $member['name'],
      ]],
    ]));

    $created = $response['tickets'][0] ?? null;
    if ($created === null) {
      if (($response['code'] ?? null) === self::EVENT_NOT_PUBLISHED) {
        return null;
      }
      $reason = $response['errors'][0]['message'] ?? $response['message'] ?? 'no response';
      throw new RuntimeException('Zoom Events did not create a ticket: ' . $reason);
    }

    db_save_zoom_events_ticket($member['badge_no'], $created['ticket_id'], $created['event_join_link'], $email);
    // Two requests can race to create the ticket. The first saved one wins
    // and the other is dropped, so read back whichever is stored.
    return db_get_zoom_events_ticket($member['badge_no'])['event_join_link'];
  }

  private function auth_token() {
    $cached = db_get_cached_auth_token('zoom_events');
    if ($cached && $cached['expires_at'] > time() + self::TOKEN_EXPIRY_MARGIN) {
      return $cached['auth_token'];
    }

    require_config('zoom_events', 'client_id', 'client_secret', 'account_id');
    $response = api_call('https://zoom.us/oauth/token', [
      'Authorization: Basic ' . base64_encode(ZOOM_EVENTS_CLIENT_ID . ':' . ZOOM_EVENTS_CLIENT_SECRET),
      'Content-Type: application/x-www-form-urlencoded',
    ], http_build_query([
      'grant_type' => 'account_credentials',
      'account_id' => ZOOM_EVENTS_ACCOUNT_ID,
    ]));
    if (!isset($response['access_token'])) {
      throw new RuntimeException('Zoom did not issue an access token: ' . ($response['reason'] ?? 'no response'));
    }

    db_cache_auth_token('zoom_events', $response['access_token'], time() + (int) $response['expires_in']);
    return $response['access_token'];
  }
}
