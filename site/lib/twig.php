<?php

require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth_functions.php');
require_once(getenv('CONFIG_LIB_DIR') . '/analytics.php');
require_once(getenv('CONFIG_LIB_DIR') . '/auth/providers.php');
require_once(getenv('CONFIG_LIB_DIR') . '/csrf.php');
require_once(getenv('CONFIG_LIB_DIR') . '/secure_links.php');

use Twig\Environment;
use Twig\Extra\Markdown\DefaultMarkdown;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

// A template and its variables, for a page to include, such as a login
// provider's form on the login page: {% include form.name with form.context %}
final class Template {
  public function __construct(public readonly string $name, public readonly array $context = []) {}
}

// Where production keeps compiled templates. The image is rebuilt for every
// deploy, so the cache never outlives the templates it was compiled from.
const TWIG_CACHE_DIR = '/var/cache/twig';

// The Twig environment every page and email renders with, built once per
// request. Templates are in site/lib/templates and autoescape as HTML; see
// "Templates" in docs/development.md.
function twig(): Environment {
  static $twig = null;
  if ($twig !== null) {
    return $twig;
  }

  $development = getenv('RUNTIME_ENV') === 'development';
  $twig = new Environment(new FilesystemLoader(getenv('CONFIG_LIB_DIR') . '/templates'), [
    'cache' => $development ? false : TWIG_CACHE_DIR,
    'auto_reload' => false,
    // A misspelt variable fails loudly in development, and renders as
    // nothing rather than a 500 in production
    'strict_variables' => $development,
  ]);

  $twig->addExtension(new MarkdownExtension());
  $twig->addRuntimeLoader(new class implements RuntimeLoaderInterface {
    public function load($class) {
      if (MarkdownRuntime::class === $class) {
        return new MarkdownRuntime(new DefaultMarkdown());
      }
      return null;
    }
  });

  // Everything a template can call. Anything else it needs comes from the
  // page's context.
  $twig->addFunction(new TwigFunction('asset', 'get_internal_url'));
  $twig->addFunction(new TwigFunction('csrf_field', fn() => $twig->render('partials/csrf-field.html.twig'), ['is_safe' => ['html']]));
  $twig->addFunction(new TwigFunction('csrf_token', 'csrf_token'));
  $twig->addFunction(new TwigFunction('secure_token', 'create_secure_token'));
  $twig->addFunction(new TwigFunction('has_permission', 'current_user_has_permission'));
  $twig->addFilter(new TwigFilter('initials', 'member_initials'));
  $twig->addFilter(new TwigFilter('avatar_colour', 'member_avatar_colour'));
  // A value as JavaScript, for inside a <script> only. Strings can't contain
  // < > & ' or ", so they can't end the script or break out of it.
  $twig->addFilter(new TwigFilter('json', fn($value) => json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), ['is_safe' => ['html']]));

  return $twig;
}

// Renders a page that extends layout.html.twig. The page's template sets
// its title, description and breadcrumbs; see layout.html.twig.
function render_page(string $template, array $context = []): void {
  // Starts the CSRF cookie while headers can still be sent
  csrf_token();
  record_analytics(member_type(), 'request', $_SERVER['REQUEST_URI']);
  echo twig()->render($template, ['site' => site_context()] + $context);
}

function render_404(): never {
  http_response_code(404);
  render_page('pages/404.html.twig');
  exit;
}

// What the layout and any page can know about the portal and the visitor
function site_context(): array {
  $user = null;
  if (isset($GLOBALS['app-data']['username'])) {
    $anonymous = is_anonymous();
    $user = [
      'name' => $GLOBALS['app-data']['username'],
      'badge_no' => $anonymous ? null : get_current_user_badge_no(),
      'anonymous' => $anonymous,
      'login_link' => make_login_link(),
      'can_switch' => !$anonymous && count(auth_switchable_members()) > 1,
    ];
  }

  return [
    'con_name' => CON_NAME,
    'con_short_name' => CON_SHORT_NAME,
    'con_website' => CON_WEBSITE,
    'registration_url' => REGISTRATION_URL,
    'support_email' => SUPPORT_EMAIL,
    'registration_email' => REGISTRATION_EMAIL,
    'tech_email' => TECH_EMAIL,
    'programme_email' => PROGRAMME_EMAIL,
    'request_uri' => $_SERVER['REQUEST_URI'],
    'year' => date('Y'),
    'user' => $user,
  ];
}

function get_internal_url($script) {
  return $script . '?v=' . filemtime(getenv('CONFIG_WEB_DIR') . $script);
}

function member_initials($name) {
  $parts = preg_split('/\s+/', trim($name));
  if (count($parts) === 1) {
    return strtoupper(substr($parts[0], 0, 1));
  }
  return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
}

// One of the four avatar colours, picked from the digits of a badge number
function member_avatar_colour($badge_no) {
  return ((int) preg_replace('/\D/', '', $badge_no) % 4) + 1;
}

// The page number asked for in ?page=, at least 1
function current_page_number() {
  return max(1, (int) ($_GET['page'] ?? 1));
}

// The links ui.pagination() in ui.html.twig shows for page $page of
// $total_pages: the first and last, two either side of this one, and
// previous and next. $params are the other query parameters each link keeps,
// such as a search. Empty when there is only one page.
function pagination_links($page, $total_pages, $params = []) {
  if ($total_pages <= 1) {
    return [];
  }
  $url = fn($number) => '?' . http_build_query(array_merge($params, ['page' => $number]));
  $numbers = array_unique(array_merge([1], range(max(1, $page - 2), min($total_pages, $page + 2)), [$total_pages]));

  $links = [];
  $previous = 0;
  foreach ($numbers as $number) {
    $links[] = [
      'number' => $number,
      'url' => $url($number),
      'current' => $number === $page,
      'gap_before' => $number > $previous + 1,
    ];
    $previous = $number;
  }

  return [
    'previous' => $page > 1 ? $url($page - 1) : null,
    'next' => $page < $total_pages ? $url($page + 1) : null,
    'links' => $links,
  ];
}
