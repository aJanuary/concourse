<?php

require_once(getenv('CONFIG_LIB_DIR') . '/api.php');

/*
Body: a list of item ids to delete
*/
handle_request(function($data) {
  if (!array_is_list($data)) {
    throw new ClientException("ERR_MALFORMED_BODY", "Expected a list of item ids");
  }

  foreach ($data as $item_id) {
    db_delete_prog_session($item_id);
  }
  return ['result' => 'success'];
});
