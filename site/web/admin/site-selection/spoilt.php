<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');

check_permission('manage-tokens');

const PER_PAGE = 50;

$page = current_page_number();
$total = db_count_spoilt_site_selection_tokens();
$total_pages = max(1, (int) ceil($total / PER_PAGE));
$tokens = db_get_spoilt_site_selection_tokens(PER_PAGE, ($page - 1) * PER_PAGE);

render_page('pages/admin/site-selection/spoilt.html.twig', [
  'total' => $total,
  'first' => ($page - 1) * PER_PAGE + 1,
  'tokens' => array_map(fn($token) => $token + ['spoilt' => site_selection_format_time($token['spoilt_at'])], $tokens),
  'pages' => pagination_links($page, $total_pages),
]);
