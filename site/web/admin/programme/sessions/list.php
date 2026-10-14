<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
  
render_header(
  'Manage sessions',
  'Manage sessions.',
  ['Home' => '/', 'Manage programme' => '/admin/programme/list', 'Manage sessions']
);
?>
  <article>
    <h2>Manage sessions</h2>
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
    <ul>
      <li><a href="/admin/programme/sessions/edit">Add session...</a></li>
<?php
        $sessions = db_get_prog_sessions();
        foreach ($sessions as $session) {
          $subtitle = null;
          if ($session['title']) {
            $label = $session['title'];
            $subtitle = $session['item_id'];
          } else {
            $label = $session['item_id'];
          }
?>
          <li>
            <a aria-label="Edit item <?php echo htmlspecialchars($label); ?>" href="/admin/programme/sessions/edit?item_id=<?php echo $session['item_id']; ?>"><?php echo htmlspecialchars($label); ?></a>
            <?php
            if ($subtitle) {
              echo '<br>' . htmlspecialchars($subtitle);
            }
            ?>
          </li>
<?php
        }
?>
    </ul>
  <article>
  <?php
  render_footer();
  ?>