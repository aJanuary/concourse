<?php

require_once(getenv('CONFIG_LIB_DIR') . '/api.php');

/*
Body: {itemId: string, title: string, destination: string, handler?: string}[]

`handler` is one of AVAILABLE_PROG_HANDLERS. Without one the session uses
DEFAULT_PROG_HANDLER.
*/
handle_request(function($data) {
  if (!array_is_list($data)) {
    throw new ClientException("ERR_MALFORMED_BODY", "Expected a list of sessions");
  }

  // Validate everything first so a bad entry doesn't leave the batch half applied
  foreach ($data as $post) {
    if (!is_array($post) || !is_string($post['itemId'] ?? null) || $post['itemId'] === '') {
      throw new ClientException("ERR_MALFORMED_BODY", "Every session needs an itemId");
    }
    if (isset($post['handler']) && !in_array($post['handler'], AVAILABLE_PROG_HANDLERS, true)) {
      throw new ClientException("ERR_UNKNOWN_HANDLER", 'Unknown handler ' . json_encode($post['handler']));
    }
  }

  foreach ($data as $post) {
    db_upsert_prog_session($post['itemId'], $post['title'] ?? null, $post['destination'] ?? '', $post['handler'] ?? null);
  }
  return ['result' => 'success'];
});
