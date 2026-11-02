<?php
  require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/db.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/analytics.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/secure_links.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/csrf.php');
  require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

  // The top and bottom of layout.html.twig, for pages not yet moved to Twig
  function render_header($title, $description, $breadcrumbs, $is_home = false) {
    // Starts the CSRF cookie while headers can still be sent
    csrf_token();
    record_analytics(member_type(), 'request', $_SERVER['REQUEST_URI']);
    echo twig()->render('partials/header.html.twig', [
      'site' => site_context(),
      'title' => $title,
      'description' => $description,
      'breadcrumbs' => $breadcrumbs,
      'is_home' => $is_home,
    ]);
  }

  function render_footer() {
    echo twig()->render('partials/footer.html.twig', ['site' => site_context()]);
  }

  function render_user_select($members, $redirect = '/') {
    echo twig()->render('partials/user-select.html.twig', [
      'site' => site_context(),
      'members' => $members,
      'redirect' => $redirect,
    ]);
  }

  // The page number asked for in ?page=, at least 1
  function current_page_number() {
    return max(1, (int) ($_GET['page'] ?? 1));
  }

  function render_pagination($page, $total_pages, $params = []) {
    echo twig()->render('partials/pagination.html.twig', ['pages' => pagination_links($page, $total_pages, $params)]);
  }
