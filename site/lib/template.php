<?php
  require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/analytics.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/secure_links.php');

  function get_internal_url($script) {
    return $script . '?v=' . filemtime(getenv('CONFIG_WEB_DIR') . $script);
  }

  function render_header($title, $description, $breadcrumbs, $is_home = false) {
    record_analytics(member_type(), 'request', $_SERVER['REQUEST_URI']);
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
    <script src="<?php echo get_internal_url("/resources/dropdown.js"); ?>" defer></script>
    <script src="<?php echo get_internal_url("/resources/form.js"); ?>" defer></script>
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
        if (isset($GLOBALS['app-data']['username'])) {
        ?>
          <div class="username">
            <?php
            if (is_anonymous()) {
            ?>
              <a href="<?php echo htmlspecialchars(make_login_link()); ?>">Log in</a>
            <?php
            } else {
            ?>
              <div id="user-dropdown">
                <input type="checkbox" id="user-menu-toggle" role="button" aria-haspopup="true" aria-controls="user-dropdown-menu">
                <label for="user-menu-toggle" id="user-menu-label"><?php echo htmlspecialchars($GLOBALS['app-data']['username']); ?></label>
                <div id="user-dropdown-menu" role="menu">
                  <a href="/profile" role="menuitem">Profile</a>
                  <?php
                  if (count(auth_switchable_members()) > 1) {
                  ?>
                    <a href="/switch-member?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" role="menuitem">Switch member</a>
                  <?php
                  }
                  ?>
                  <a href="/logout" role="menuitem">Log out</a>
                </div>
              </div>
            <?php
            }
            ?>
          </div>
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
            <a href="<?php echo $url; ?>"><?php echo $name; ?></a>
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
          <li><a href="/help">Help</a></li>
          <li><a href="/site-map">Site map</a></li>
          <li><a href="/attributions">Icon attributions</a></li>
          <li><a href="/privacy-policy">Privacy policy</a></li>
        </ul>
      </div>
    </footer>
  </body>
</html>
<?php
  }

  function member_initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    if (count($parts) === 1) {
      return strtoupper(substr($parts[0], 0, 1));
    }
    return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
  }

  // One button per member. Each posts a token bound to the current member, so
  // a form from another site or another login cannot switch the session.
  function render_user_select($members, $redirect = '/') {
    $current = get_current_user_badge_no();
?>
    <ul id="user-select">
      <?php foreach ($members as $member) { ?>
        <li>
          <form method="post" action="/switch-member">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars(create_secure_token('/switch-member', ['from' => $current, 'badge_no' => $member['badge_no'], 'redirect' => $redirect], 300)); ?>">
            <button type="submit"<?php echo $member['badge_no'] === $current ? ' aria-current="true"' : ''; ?>>
              <span class="avatar color-<?php echo ((int) preg_replace('/\D/', '', $member['badge_no']) % 4) + 1; ?>"><?php echo htmlspecialchars(member_initials($member['name'])); ?></span>
              [<?php echo htmlspecialchars($member['badge_no']); ?>] <?php echo htmlspecialchars($member['name']); ?>
            </button>
          </form>
        </li>
      <?php } ?>
    </ul>
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