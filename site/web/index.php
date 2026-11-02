<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

$cards = [
  'Info' => [
    [
      'name' => 'time',
      'title' => 'Convention time',
      'subtitle' => '',
      'link' => '/convention-time',
      'card-permission' => 'see-time',
      'live-card' => true,
    ], [
      'name' => 'maps',
      'title' => 'Maps',
      'subtitle' => '',
      'link' => '/maps',
      'card-permission' => 'see-maps',
    ], [
      'name' => 'times',
      'title' => 'Opening times',
      'subtitle' => '',
      'link' => '/times',
      'card-permission' => 'see-times',
    ], [
      'name' => 'readme',
      'title' => 'Convention guide',
      'subtitle' => 'Readme',
      'link' => current_user_has_permission('see-readme-redirect') ? 'https://example.com/convention-readme' : '/convention-guide',
      'card-permission' => 'see-readme',
    ], [
      'name' => 'newsletter',
      'title' => 'Newsletter',
      'subtitle' => '',
      'link' => current_user_has_permission('see-newsletter-redirect') ? 'https://example.com/newsletter' : '/newsletter',
      'card-permission' => 'see-help',
    ], [
      'name' => 'accessibility',
      'title' => 'Accessibility',
      'subtitle' => '',
      'link' => 'https://example.com/accessibility/',
      'card-permission' => 'see-accessibility',
    ], [
      'name' => 'childcare',
      'title' => 'Childcare',
      'subtitle' => '',
      'link' => 'https://example.com/childcare/',
      'card-permission' => 'has-childcare'
    ], [
      'name' => 'coc',
      'title' => 'Code of Conduct',
      'subtitle' => '',
      'link' => 'https://example.com/code-of-conduct/',
      'card-permission' => 'see-coc',
    ], [
      'name' => 'help',
      'title' => 'Get help',
      'subtitle' => 'Info desk, help desks and contacts',
      'link' => '/help',
      'card-permission' => 'see-help',
    ], [
      'name' => 'website',
      'title' => 'Website',
      'subtitle' => '',
      'link' => 'https://example.com/',
      'card-permission' => 'see-website',
    ]
  ],
  'Programme' => [
    [
      'name' => 'guide',
      'title' => 'Programme guide',
      'subtitle' => 'ConClár',
      'link' => current_user_has_permission('see-guide-redirect') ? 'https://example.com/programme-guide' : '/programme-guide',
      'card-permission' => 'see-guide',
    ], [
      'name' => 'stream',
      'title' => 'Stream and replay',
      'subtitle' => 'Live streams and replays',
      'link' => '/stream',
      'card-permission' => 'see-stream',
    ], [
      'name' => 'signups',
      'title' => 'Sign-ups',
      'subtitle' => 'For items with limited space',
      'link' => '/sign-ups',
      'card-permission' => 'see-signups',
    ], [
      'name' => 'tours',
      'title' => 'Tours and activities',
      'subtitle' => 'Beyond the programme',
      'link' => 'https://example.com/tours-and-activities',
      'card-permission' => 'see-tours',
    ], [
      'name' => 'virtual-gathering',
      'title' => 'Virtual gatherings',
      'subtitle' => 'Host your own hangout',
      'link' => '/virtual-gatherings',
      'card-permission' => 'see-virtual-gathering'
    ], [
      'name' => 'chat',
      'title' => 'Chat online',
      'subtitle' => 'Discord',
      'link' => '/chat',
      'card-permission' => 'see-discord',
    ],
  ],
  'Participate' => [
    [
      'name' => 'volunteer',
      'title' => 'Volunteer',
      'subtitle' => '',
      'link' => 'https://example.com/volunteer',
      'card-permission' => 'see-website',
    ], [
      'name' => 'upload-photo',
      'title' => 'Upload your photo',
      'subtitle' => 'For online members',
      'link' => '/upload-photo',
      'card-permission' => 'see-upload-photo',
    ], [
      'name' => 'itinerary',
      'title' => 'Your participant schedule',
      'subtitle' => 'Planorama',
      'link' => 'https://example.com/participant-schedule',
      'card-permission' => 'see-itinerary'
    ], [
      'name' => 'participant-guides',
      'title' => 'Participant guides',
      'subtitle' => 'Programme',
      'link' => '/guides/list',
      'card-permission' => 'see-participant-guides'
    ], [
      'name' => 'upload-presentation',
      'title' => 'Upload presentation',
      'subtitle' => 'Programme participants and presenters',
      'link' => '/upload-presentation',
      'card-permission' => 'see-upload-presentation'
    ], [
      'name' => 'business-meeting',
      'title' => 'Business Meeting',
      'subtitle' => 'WSFS',
      'link' => '/business-meeting',
      'card-permission' => 'see-business-meeting'
    ], [
      'name' => 'site-selection',
      'title' => 'Site selection',
      'subtitle' => 'Vote for the ' . SITE_SELECTION['year'] . ' Worldcon',
      'link' => '/site-selection',
      'card-permission' => 'see-site-selection'
    ]
  ],
  'Other' => [
    [
      'name' => 'gallery',
      'title' => 'Art show',
      'subtitle' => '',
      'link' => 'https://example.com/art-show',
      'card-permission' => 'see-gallery',
    ], [
      'name' => 'benefits',
      'title' => 'Member benefits',
      'subtitle' => '',
      'link' => 'https://example.com/member-benefits',
      'card-permission' => 'see-benefits',
    ], [
      'name' => 'manage-roles',
      'title' => 'Manage roles',
      'subtitle' => 'Admin',
      'link' => '/admin/roles',
      'card-permission' => 'manage-roles',
    ], [
      'name' => 'manage-discord',
      'title' => 'Discord ids',
      'subtitle' => 'Admin',
      'link' => '/admin/discord',
      'card-permission' => 'manage-discord-ids',
    ], [
      'name' => 'manage-programme',
      'title' => 'Manage programme',
      'subtitle' => 'Admin',
      'link' => '/admin/programme/list',
      'card-permission' => 'manage-programme',
    ], [
      'name' => 'programme-reports',
      'title' => 'Programme reports',
      'subtitle' => 'Admin',
      'link' => '/admin/programme-reports',
      'card-permission' => 'see-programme-reports',
    ], [
      'name' => 'manage-login-sessions',
      'title' => 'Manage login sessions',
      'subtitle' => 'Admin',
      'link' => '/admin/login-sessions',
      'card-permission' => 'manage-login-sessions',
    ], [
      'name' => 'manage-business-meeting',
      'title' => 'Manage business meeting',
      'subtitle' => 'Admin',
      'link' => '/admin/business-meeting/list',
      'card-permission' => 'manage-business-meeting',    ], [
      'name' => 'site-selection-desk',
      'title' => 'Site selection desk',
      'subtitle' => 'Admin',
      'link' => '/admin/site-selection/desk',
      'card-permission' => 'sell-tokens',
    ], [
      'name' => 'manage-site-selection',
      'title' => 'Manage site selection',
      'subtitle' => 'Admin',
      'link' => '/admin/site-selection/list',
      'card-permission' => 'manage-tokens',
    ]
  ]
];

// Each category's cards the member can use
$categories = [];
foreach ($cards as $category => $category_cards) {
  $category_cards = array_filter($category_cards, function($card) {
    return current_user_has_permission($card['card-permission']);
  });
  if (!empty($category_cards)) {
    $categories[$category] = $category_cards;
  }
}

// Cards the member can't use yet but has been told are on their way, with
// the card's permission followed by -coming-soon. Several cards share a
// permission, so each card is only listed once.
$coming_soon_cards = [];
foreach ($cards as $category_cards) {
  foreach ($category_cards as $card) {
    if (!current_user_has_permission($card['card-permission'])
        && current_user_has_permission($card['card-permission'] . '-coming-soon')) {
      $coming_soon_cards[$card['name']] = $card;
    }
  }
}

if (current_user_has_permission('online')) {
  $default_favs = ['chat', 'upload-photo', 'newsletter', 'guide', 'signups', 'stream'];
} else {
  $default_favs = ['chat', 'newsletter', 'maps', 'guide', 'signups'];
}
if (current_user_has_permission('programme-participant')) {
  $default_favs[] = 'itinerary';
}
if (current_user_has_permission('childcare')) {
  $default_favs[] = 'childcare';
}
if (current_user_has_permission('see-business-meeting')) {
  $default_favs[] = 'business-meeting';
}
if (current_user_has_permission('see-site-selection')) {
  $default_favs[] = 'site-selection';
}

render_page('pages/index.html.twig', [
  'categories' => $categories,
  'coming_soon_cards' => array_values($coming_soon_cards),
  'default_favs' => $default_favs,
  'badge_no' => get_current_user_badge_no() ?? '',
  'timezone' => TIMEZONE,
]);
