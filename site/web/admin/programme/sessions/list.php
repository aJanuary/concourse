<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
  
render_header("Manage RCE sessions.");
?>
  
  <a href="/admin/programme/list" class="back">&lt; Back to Manage programme</a>
  
  <article>
    <h2>Manage RCE sessions</h2>
    <ul>
      <li><a href="/admin/programme/sessions/edit">Add session...</a></li>
<?php
        $sessions = db_get_prog_sessions();
        foreach ($sessions as $session) {
?>
          <li><a href="/admin/programme/sessions/edit?item_id=<?php echo $session['item_id']; ?>"><?php echo $session['item_id']; ?></a></li>
<?php
        }
?>
    </ul>
    <script>
      if (window.location.search.includes('error')) {
        var $toast = document.createElement('p');
        $toast.id = 'error-toast';
        $toast.textContent = 'Error: ' + new URLSearchParams(window.location.search).get('error');
        $toast.addEventListener('click', e => {
          e.target.remove();
        });
        document.body.appendChild($toast);
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    </script>
  <?php
  render_footer();
  ?>