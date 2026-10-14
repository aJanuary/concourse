<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/signups.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

render_header(
  'Manage sign-ups',
  'Manage sign-ups.',
  ['Home' => '/', 'Manage programme' => '/admin/programme/list', 'Manage sign-ups']
);
?>
  <article>
    <h2>Manage sign-ups</h2>
<?php
if (isset($_GET['error'])) {
?>
    <p class="error"><img src="/resources/error.svg" alt="error" aria-label="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
<?php
}
?>
    <ul>
      <li><a href="/admin/programme/signups/edit">Add sign-up...</a></li>
      <li><a href="/admin/programme/signups/bulk-add">Bulk add sign-ups...</a></li>
<?php
foreach (signups_sort(db_get_signups()) as $signup) {
?>
      <li>
        <a aria-label="Edit sign-up <?php echo htmlspecialchars($signup['title']); ?>" href="/admin/programme/signups/edit?id=<?php echo urlencode((string) $signup['id']); ?>"><?php echo htmlspecialchars($signup['title']); ?></a>
        <br><?php echo htmlspecialchars($signup['category']); ?><?php echo $signup['session_start'] !== null ? ', ' . htmlspecialchars(signup_format_time($signup['session_start'])) : ''; ?>
      </li>
<?php
}
?>
    </ul>
  </article>
<?php
render_footer();
?>
