<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/db.php');

function conclar_cors_headers() {
    $request_origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (in_array($request_origin, CONCLAR_ALLOWED_ORIGINS, true)) {
        header('Access-Control-Allow-Origin: ' . $request_origin);
    }
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
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT item_id, selected FROM conclar_selections WHERE badge_no = ? AND app_id = ?");
    try {
        $stmt->bind_param("ss", $badge_no, $app_id);
        $stmt->execute();
        $stmt->bind_result($item_id, $selected);
        $selections = [];
        while ($stmt->fetch()) {
            $selections[$item_id] = (bool)$selected;
        }
    } finally {
        $stmt->close();
    }

    return $selections;
}

function conclar_patch_selections($badge_no, $app_id, $selections) {
    global $mysqli;

    $mysqli->begin_transaction();
    try {
        $stmt = $mysqli->prepare("INSERT INTO conclar_selections (badge_no, app_id, item_id, selected) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE selected = VALUES(selected)");
        try {
            foreach ($selections as $item_id => $selected) {
                $selected_int = $selected ? 1 : 0;
                $stmt->bind_param("sssi", $badge_no, $app_id, $item_id, $selected_int);
                $stmt->execute();
            }
        } finally {
            $stmt->close();
        }

        $mysqli->commit();
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
}
?>
