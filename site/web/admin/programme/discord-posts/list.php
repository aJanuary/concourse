<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}
  
render_header("Manage discord posts", "Manage Discord posts.");
?>
  
  <a href="/admin/programme/list" class="back">&lt; Back to Manage programme</a>
  
  <article>
    <h2>Manage Discord posts</h2>
    <p>Warning: These may be overriden by Watson</p>
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
      <li><a href="/admin/programme/discord-posts/edit">Add post...</a></li>
<?php
        $posts = db_get_discord_posts();
        foreach ($posts as $post) {
?>
          <li><a aria-label="Edit item <?php echo $post['item_id']; ?>" href="/admin/programme/discord-posts/edit?item_id=<?php echo $post['item_id']; ?>"><?php echo $post['item_id']; ?></a></li>
<?php
        }
?>
    </ul>
  <?php
  render_footer();
  ?>