<?php

// Sends members to the item's Discord post. /deep-link/chat looks the post up
// by item id, checks the member has joined the Discord server and walks them
// through joining if not, so the item's destination is not used.
class DiscordHandler implements ProgHandler {
  public function join(array $item): ProgHandlerResult {
    if ($item['kind'] !== 'session') {
      return ProgHandlerResult::message('No replay', 'Replays are not available through Discord.');
    }
    return ProgHandlerResult::redirect('/deep-link/chat?item_id=' . urlencode($item['item_id']));
  }
}
