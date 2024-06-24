<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
  
render_header(
  'Manage RCE replays',
  'Manage RCE replays.',
  ['Home' => '/', 'Manage programme' => '/admin/programme/list', 'Manage RCE replays']
);
?>
  <article>
    <h2>Manage <abbr title="RingCentral Events">RCE</abbr> replays</h2>
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
      <li><a href="/admin/programme/replay/edit">Add replay...</a></li>
<?php
        $replays = db_get_replays();
        foreach ($replays as $replay) {
          if ($replay['title']) {
            $label = $replay['title'] . ' (' . $replay['item_id'] . ')';
          } else {
            $label = $replay['item_id'];
          }
?>
          <li><a aria-label="Edit item <?php echo $label; ?>" href="/admin/programme/replay/edit?item_id=<?php echo $replay['item_id']; ?>"><?php echo $label; ?></a></li>
<?php
        }
?>
    </ul>
  <article>
  <?php
  render_footer();
  ?>