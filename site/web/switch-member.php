<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');

$members = auth_switchable_members();
if (count($members) < 2) {
  render_404();
}

$current = get_current_user_badge_no();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = verify_secure_link('/switch-member', (string) ($_POST['token'] ?? ''));
  if (!is_array($data) || $data['from'] !== $current) {
    header('Location: /switch-member');
    exit;
  }

  foreach ($members as $member) {
    if ($member['badge_no'] === $data['badge_no']) {
      if ($member['badge_no'] !== $current) {
        $old_session_id = get_current_session_id();
        make_session($member['badge_no']);
        db_delete_session($old_session_id);
      }
      header('Location: ' . (is_valid_redirect($data['redirect']) ? $data['redirect'] : '/'));
      exit;
    }
  }

  header('Location: /switch-member');
  exit;
}

$redirect = (string) ($_GET['redirect'] ?? '/');

render_header(
  'Switch member',
  'Switch to a different member.',
  ['Home' => '/', 'Switch member']
);
?>
<article>
  <h2>Switch member</h2>
  <p>Select which member you would like to switch to:</p>
  <?php render_user_select($members, is_valid_redirect($redirect) ? $redirect : '/'); ?>
</article>
<?php
render_footer();
