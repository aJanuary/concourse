<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
  
render_header();
?>
  
  <a href="/admin/programme/list" class="back">&lt; Back to Manage programme</a>
  
  <article>
    <h3>Manage RCE stages</h3>
    <ul>
      <li><a href="/admin/programme/stages/edit">Add stage...</a></li>
<?php
        $stages = db_get_stages();
        foreach ($stages as $stage) {
?>
          <li><a href="/admin/programme/stages/edit?room_id=<?php echo $stage['room_id']; ?>"><?php echo $stage['room_id']; ?></a></li>
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