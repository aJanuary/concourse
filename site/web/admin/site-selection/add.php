<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');

check_permission('manage-tokens');

$error = null;
$text = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $text = (string) ($_POST['tokens'] ?? '');
  [$entries, $error] = site_selection_tokens_from_csv($text);
  if ($error === null) {
    $existing = db_get_existing_site_selection_tokens(array_column($entries, 'token'));
    if ($existing) {
      $labels = array_column($entries, 'label', 'token');
      $named = array_map(fn($token) => $labels[$token], array_slice($existing, 0, 5));
      $error = (count($existing) === 1 ? 'One key is' : count($existing) . ' keys are') . ' already in the pool, labelled ' . implode(', ', $named) . (count($existing) > 5 ? ' and others' : '') . '. Remove ' . (count($existing) === 1 ? 'it' : 'them') . ' and try again.';
    }
  }
  if ($error === null) {
    db_add_site_selection_tokens($entries, get_current_user_badge_no());
    header('Location: /admin/site-selection/list?' . http_build_query(['added' => count($entries)]));
    exit;
  }
}

render_page('pages/admin/site-selection/add.html.twig', [
  'error' => $error,
  'text' => $text,
]);
