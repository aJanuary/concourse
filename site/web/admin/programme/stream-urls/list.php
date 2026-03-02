<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

render_header(
  'Manage stream URLs',
  'Manage stream URLs.',
  ['Home' => '/', 'Manage programme' => '/admin/programme/list', 'Manage stream URLs']
);
?>
  <article>
    <h2>Manage stream URLs</h2>
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
      <li><a href="/admin/programme/stream-urls/edit">Add stream URL...</a></li>
<?php
        $stream_urls = db_get_stream_urls();
        foreach ($stream_urls as $stream_url) {
          if ($stream_url['title']) {
            $label = $stream_url['title'];
            $subtitle = $stream_url['room_id'];
          } else {
            $label = $stream_url['room_id'];
            $subtitle = null;
          }
?>
          <li>
            <a aria-label="Edit stream URL <?php echo htmlspecialchars($label); ?>" href="/admin/programme/stream-urls/edit?room_id=<?php echo $stream_url['room_id']; ?>"><?php echo htmlspecialchars($label); ?></a>
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
  </article>
  <?php
  render_footer();
  ?>
