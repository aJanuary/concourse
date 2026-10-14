<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
require_once(getenv('CONFIG_LIB_DIR') . '/requests.php');

function conclar_cors_headers() {
  $request_origin = $_SERVER['HTTP_ORIGIN'] ?? '';
  if (in_array($request_origin, CONCLAR_ALLOWED_ORIGINS, true)) {
    header('Access-Control-Allow-Origin: ' . $request_origin);
  }
  header('Vary: Origin');
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Allow-Methods: GET, PATCH, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
  }
}

function conclar_error($code, $message) {
  return json_encode(['error' => ['code' => $code, 'message' => $message]]);
}

function conclar_validate_app_id($app_id) {
  return in_array($app_id, CONCLAR_VALID_APP_IDS, true);
}

function conclar_get_selections($badge_no, $app_id) {
  $rows = db_fetch_rows("SELECT item_id, selected FROM conclar_selections WHERE badge_no = ? AND app_id = ?", [$badge_no, $app_id]);

  $selections = [];
  foreach ($rows as $row) {
    $selections[$row['item_id']] = (bool) $row['selected'];
  }

  return $selections;
}

function conclar_patch_selections($badge_no, $app_id, $selections) {
  global $pdo;

  $pdo->beginTransaction();
  try {
    foreach ($selections as $item_id => $selected) {
      // 1 and 0 rather than booleans, as PDO sends false to Postgres as ''
      db_upsert(
        'conclar_selections',
        ['badge_no', 'app_id', 'item_id'],
        ['selected'],
        ['badge_no' => $badge_no, 'app_id' => $app_id, 'item_id' => (string) $item_id, 'selected' => $selected ? 1 : 0]
      );
    }
    $pdo->commit();
  } catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
  }
}

// Each starred item with how many members starred it, and how many of those
// held the online and in-person roles. Most starred first.
function conclar_get_favourite_counts($app_id) {
  return db_fetch_rows(
    "SELECT s.item_id, COUNT(*) AS total, COUNT(online.badge_no) AS online, COUNT(in_person.badge_no) AS in_person
     FROM conclar_selections s
     LEFT JOIN (SELECT member_roles.badge_no FROM member_roles JOIN roles USING (role_id) WHERE roles.name = 'online') online ON online.badge_no = s.badge_no
     LEFT JOIN (SELECT member_roles.badge_no FROM member_roles JOIN roles USING (role_id) WHERE roles.name = 'in-person') in_person ON in_person.badge_no = s.badge_no
     WHERE s.app_id = ? AND s.selected = ?
     GROUP BY s.item_id
     ORDER BY total DESC, s.item_id",
    [$app_id, 1]
  );
}

function conclar_count_members_with_favourites($app_id) {
  return (int) db_fetch_value("SELECT COUNT(DISTINCT badge_no) FROM conclar_selections WHERE app_id = ? AND selected = ?", [$app_id, 1]);
}

// Item id => title from the programme JSON, or an empty array when no
// schedule_url is configured or it cannot be fetched
function conclar_fetch_item_titles() {
  if (CONCLAR_SCHEDULE_URL === '') {
    return [];
  }

  $response = api_call(CONCLAR_SCHEDULE_URL, [], false, null, 15);
  if (!is_array($response) || !isset($response['schedule']) || !is_array($response['schedule'])) {
    return [];
  }

  $titles = [];
  foreach ($response['schedule'] as $item) {
    if (isset($item['id'], $item['title'])) {
      $titles[(string) $item['id']] = $item['title'];
    }
  }
  return $titles;
}
