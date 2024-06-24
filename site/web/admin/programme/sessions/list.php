<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
  
render_header(
  'Manage RCE sessions',
  'Manage RCE sessions.',
  ['Home' => '/', 'Manage programme' => '/admin/programme/list', 'Manage RCE sessions']
);
?>
  <article>
    <h2>Manage <abbr title="RingCentral Events">RCE</abbr> sessions</h2>
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
          if ($session['title']) {
            $label = $session['title'] . ' (' . $session['item_id'] . ')';
          } else {
            $label = $session['item_id'];
          }
?>
          <li><a aria-label="Edit item <?php echo $label; ?>" href="/admin/programme/sessions/edit?item_id=<?php echo $session['item_id']; ?>"><?php echo $label; ?></a></li>
<?php
        }
?>
    </ul>
  <article>
  <?php
  render_footer();
  ?>