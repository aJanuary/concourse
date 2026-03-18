<?php

require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/conclar.php');

conclar_cors_headers();

header('Content-Type: application/json; charset=utf-8');

$app_id = 'e26';

if (!is_logged_in()) {
    http_response_code(401);
    echo conclar_error('unauthorized', 'Session cookie missing or invalid');
    exit;
}

$badge_no = get_current_user_badge_no();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $selections = conclar_get_selections($badge_no, $app_id);
    echo json_encode(['selections' => empty($selections) ? new stdClass() : $selections]);
} elseif ($method === 'PATCH') {
    // CSRF check: verify Origin header matches allowed origin
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (!in_array($origin, CONCLAR_ALLOWED_ORIGINS, true)) {
        http_response_code(403);
        echo conclar_error('forbidden', 'Origin not allowed');
        exit;
    }

    // Validate Content-Type
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($content_type, 'application/json') === false) {
        http_response_code(415);
        echo conclar_error('unsupported_media_type', 'Content-Type must be application/json');
        exit;
    }

    $json = file_get_contents('php://input');
    $body = json_decode($json, true);
    if ($body === null || !isset($body['selections']) || !is_array($body['selections'])) {
        http_response_code(400);
        echo conclar_error('invalid_request', 'Body must contain a selections object');
        exit;
    }

    // Validate all values are booleans before writing any
    foreach ($body['selections'] as $item_id => $selected) {
        if (!is_bool($selected)) {
            http_response_code(400);
            echo conclar_error('invalid_request', 'All selection values must be boolean');
            exit;
        }
    }

    // Check that writing these selections wouldn't exceed the max items limit
    $existing = conclar_get_selections($badge_no, $app_id);
    $new_keys = array_diff_key($body['selections'], $existing);
    if (count($existing) + count($new_keys) > CONCLAR_MAX_ITEMS) {
        http_response_code(400);
        echo conclar_error('too_many_items', 'Maximum of ' . CONCLAR_MAX_ITEMS . ' selections allowed');
        exit;
    }

    if (!empty($body['selections'])) {
        conclar_patch_selections($badge_no, $app_id, $body['selections']);
    }

    http_response_code(204);
    header_remove('Content-Type');
} else {
    http_response_code(405);
    echo conclar_error('method_not_allowed', 'Method not allowed');
}
?>
