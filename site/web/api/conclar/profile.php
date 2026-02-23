<?php

require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/conclar.php');

conclar_cors_headers();

header('Content-Type: application/json; charset=utf-8');

if (is_logged_in()) {
    $badge_no = get_current_user_badge_no();
    $name = get_current_user_name();
    echo json_encode([
        'authenticated' => true,
        'id' => $badge_no,
        'display_name' => $name,
        'logout_url' => ROOT_URL . '/logout?redirect=<return_url>',
    ]);
} else {
    echo json_encode([
        'authenticated' => false,
        'login_url' => ROOT_URL . '/login?redirect=<return_url>',
    ]);
}
?>
