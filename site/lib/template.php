<?php
  require_once('config.php');
  require_once('auth_functions.php');

  function get_internal_url($script) {
    return $script . '?v=' . hash_file('md5', getenv('CONFIG_WEB_DIR') . $script);
  }

  function render_header($title, $description, $breadcrumbs, $is_home = false) {
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php echo $description; ?>">
    <title><?php echo CON_NAME; ?> Member Portal<?php echo $title ? ' - ' . $title : ''; ?></title>
    <link rel="shortcut icon" href="/resources/favicon.png">
    <link rel="stylesheet" href="<?php echo get_internal_url("/resources/reset.css"); ?>">
    <link rel="stylesheet" href="<?php echo get_internal_url("/resources/style.css"); ?>">
  </head>
  <body <?php echo $is_home ? 'class="home"' : ''; ?>>
    <header>
      <div class="container">
        <a href="/" class="site-branding-container" rel="home">
          <div class="site-logo">
            <img width="36" height="36" src="/resources/logo-2x.webp" srcset="/resources/logo-1x.webp 1x, /resources/logo-1.5x.webp 1.5x, /resources/logo-2x.webp 2x" alt="">
          </div>
          <div class="site-branding">
            <hgroup>
              <h1><?php echo CON_SHORT_NAME; ?></h1>
              <p>Member portal</p>
            </hgroup>
          </div>
        </a>
        <?php
        if (isset($_REQUEST['username'])) {
        ?>
          <p class="username">
            <?php echo $_REQUEST['username']; ?>
            |
            <?php
            if (is_anonymous()) {
            ?>
              <a href="<?php echo make_login_link(); ?>">Log in</a>
            <?php
            } else {
            ?>
              <a href="/logout">Log out</a>
            <?php
            }
            ?>
          </p>
        <?php
        }
        ?>
      </div>
    </header>
    <main>
<?php
    if ($breadcrumbs) {
?>
      <nav class="breadcrumbs">
        <ul>
          <?php
          foreach ($breadcrumbs as $name => $url) {
          ?>
            <li>
          <?php  
            if ($name) {
          ?>
            <a href="<?php echo $url; ?>"><?php echo $name; ?></a></li>
          <?php } else {
          ?>
            <?php echo $url; ?>
          <?php
            }
          ?>
            </li>
          <?php
          }
          ?>
        </ul>
      </nav>
<?php
    }
  }

  function render_footer() {
?>
    </main>
    <footer>
      <div class="container">
        <p>&copy; <?php echo date('Y'); ?> <?php echo CON_SHORT_NAME; ?></p>
        <ul>
          <li><a href="/under-construction?Help">Help</a></li>
          <li><a href="/site-map">Site map</a></li>
          <li><a href="https://glasgow2024.org/cookie-policy-eu/">Cookie Policy (EU)</a></li>
          <li><a href="https://glasgow2024.org/about/policies/code-of-conduct/">Code of Conduct</a></li>
          <li><a href="https://glasgow2024.org/about/policies/">All Policies</a></li>
        </ul>
      </div>
    </footer>
  </body>
</html>
<?php
  }

  function render_404() {
    http_response_code(404);
    render_header(
      'Page not found',
      'Page not found.',
      ['Home' => '/', 'Page not found']
    );
?>
    <article>
      <h3>Page not found</h3>
      <p>The page you are looking for does not exist.</p>
    </article>
<?php
    render_footer();
    exit;
  }
?>