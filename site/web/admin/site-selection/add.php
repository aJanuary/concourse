<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
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

render_header(
  'Add keys',
  'Add site selection voter keys.',
  ['Home' => '/', 'Manage site selection' => '/admin/site-selection/list', 'Add keys']
);
?>
<article>
  <h2>Add keys</h2>
  <p>Paste the voter keys from the election platform, one per line, each with a label to tell it apart without showing the key, separated by a comma: <code>Batch A 001,abc-def-ghi</code>. Nothing is added unless every row is fine.</p>
<?php
if ($error !== null) {
?>
  <div class="error" id="tokens-error" role="alert">
    <img src="/resources/error.svg" alt="error" aria-label="error">
    <?php echo htmlspecialchars($error); ?>
  </div>
<?php
}
?>
  <form method="POST" class="vertical">
    <?php echo csrf_field(); ?>
    <label for="tokens">Keys, as label,key</label>
    <textarea id="tokens" name="tokens" rows="20" required class="token-csv"<?php if ($error !== null) { ?> aria-describedby="tokens-error" aria-invalid="true"<?php } ?>><?php echo htmlspecialchars($text); ?></textarea>
    <p>
      <input type="submit" value="Add keys">
      <a href="/admin/site-selection/list">Cancel</a>
    </p>
  </form>
</article>
<?php
render_footer();
?>
