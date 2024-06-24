<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('manage-programme')) {
  header('Location: /');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $zoom_url = $_POST['zoom_url'] ?: '';

  try {
    db_set_zoom_url($zoom_url);
  } catch (Exception $e) {
    header('Location: /admin/programme/zoom/edit?error=' . $e->getMessage());
    throw $e;
  }

  header('Location: /admin/programme/list');
  exit;
}

$zoom_url = db_get_zoom_url();

render_header(
  'Manage Zoom link',
  'Manage Zoom link.',
  ['Home' => '/', 'Manage programme' => '/admin/programme/list', 'Edit Zoom link']
);
?>
  <article>
    <h2>Edit Zoom link</h2>
    <form action="/admin/programme/zoom/edit" method="POST" class="vertical">
      <fieldset>
        <legend>Zoom link</legend>
        <p><label>Zoom URL: <input name="zoom_url" value="<?php echo $zoom_url; ?>"></label></p>
      </fieldset>
      <input type="submit" value="Save">
    </form>

    <script>
      if (window.location.search.includes('error')) {
          var $toast = document.createElement('p');
          $toast.role = 'alert';
          $toast.id = 'error-toast';
          $toast.textContent = 'Error: ' + new URLSearchParams(window.location.search).get('error');
          $toast.addEventListener('click', e => {
            e.target.remove();
          });
          document.body.appendChild($toast);
          window.history.replaceState({}, document.title, window.location.pathname);
        }
    </script>
  <article>
  <?php
  render_footer();
  ?>