<?php

// Live items are streamed per room. A session's destination is the id of the
// room it is in, and the room's current URL comes from prog_stream_urls so it
// can be changed without touching the sessions. Replays are YouTube or other
// video URLs.
class StreamHandler implements ProgHandler {
  public function join(array $item): ProgHandlerResult {
    if ($item['kind'] === 'replay') {
      return $this->replay($item['destination'], $item['title']);
    }

    $stream = db_get_stream_url($item['destination']);
    if (!$stream['found']) {
      return ProgHandlerResult::message(
        'Unknown room',
        "Oops, something went wrong and we don't know where you should be going. Please get in touch with the programme team to find out."
      );
    }
    if (!$stream['url']) {
      return ProgHandlerResult::message('Room not streaming', 'This room is not currently streaming. Please check back later.');
    }
    return ProgHandlerResult::redirect($stream['url']);
  }

  private function replay($url, $title) {
    $pattern = '#^https?://(?:(?:www|m)\.)?(?:youtube\.com/(?:watch\?(?:.*&)?v=|live/|embed/)|youtu\.be/)([A-Za-z0-9_-]+)#';
    if (preg_match($pattern, $url, $matches)) {
      // No-cookie so watching doesn't set tracking cookies for YouTube
      return ProgHandlerResult::embed('https://www.youtube-nocookie.com/embed/' . $matches[1], $title);
    }
    return ProgHandlerResult::redirect($url);
  }
}
