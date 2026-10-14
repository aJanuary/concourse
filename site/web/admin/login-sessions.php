<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-login-sessions')) {
  header('Location: /');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($_POST as $session_id => $value) {
    if ($value === 'on') {
      db_delete_session($session_id);
    }
  }
  header('Location: /admin/login-sessions');
  exit;
}
  
render_header(
  'Manage login sessions',
  'Manage login sessions.',
  ['Home' => '/', 'Manage login sessions']
);
?>
  <article>
    <h2>Manage login sessions</h2>
    <script>
      if (window.location.search.includes('error')) {
        var $toast = document.createElement('p');
        $toast.classList = ['error'];
        $toast.textContent = new URLSearchParams(window.location.search).get('error');
      
        var $img = document.createElement('img');
        $img.src = '/resources/error.svg';
        $img.alt = 'error';
        $img.ariaLabel = 'error';
        $toast.prepend($img);

        document.write($toast.outerHTML);
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    </script>
    <form method="POST">
      <?php echo csrf_field(); ?>
      <ul>
<?php
        $sessions = db_get_sessions();
        foreach ($sessions as $session) {
?>
          <li>
              <label>
                <input type="checkbox" name="<?php echo $session['session_id']; ?>">
                <?php echo $session['badge_no'] . ' ' . htmlspecialchars($session['name']) . ' (' . $session['session_id'] . ')'; ?>
              </label>
          </li>
<?php
        }
?>
      </ul>
      <input type="submit" value="Delete selected sessions">
    </form>
  </article>
  <?php
  render_footer();
  ?>