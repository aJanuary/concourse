<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  null,
  'The Member Portal for ' . CON_NAME . '.',
  null,
  true
);

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
      'card-permission' => 'see-rce',
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
      'subtitle' => 'In RingCentral Events',
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
      'card-permission' => 'manage-business-meeting',
    ]
  ]
];
?>

<nav>
  <div id="favs" class="category">
    <h2><img src="/resources/fav-text.svg" alt="" width="20px" height="20px"> Quick links</h2>
    <p id="fav-hint"><em>Click the heart icon on a card to add or remove it from this quick links section.</em></p>
    <div class="cards"></div>
  </div>
  <?php
  foreach ($cards as $category => $category_cards) {
    $category_cards = array_filter($category_cards, function($card) {
      return !array_key_exists('card-permission', $card) || current_user_has_permission($card['card-permission']);
    });
    if (empty($category_cards)) {
      continue;
    }
  ?>
    <div class="category">
      <h2><?php echo $category; ?></h2>
      <div class="cards">
      <?php
      foreach ($category_cards as $card) {
        if (array_key_exists('card-permission', $card) && current_user_has_permission($card['card-permission'])) {
          $open_in_new_tab = str_starts_with($card['link'], 'http');
      ?>
        <div class="card <?php echo $card['name']; ?>">
          <div class="hero"
          <?php if (array_key_exists('live-card', $card) && $card['live-card']) { ?>
              aria-label="<?php echo $card['title']; ?>"
          <?php } else { ?>
              aria-hidden="true"
          <?php } ?>
          >
          </div>
          <hgroup>
            <h3><a href="<?php echo $card['link']; ?>" <?php echo $open_in_new_tab ? 'target="_blank"' : ''; ?>><?php echo $card['title']; ?></a></h3>
            <?php
            if ($card['subtitle']) {
            ?>
            <h4><?php echo $card['subtitle']; ?></h4>
            <?php
            }
            ?>
          </hgroup>
          <button class="fav" aria-label="Add <?php echo $card['title']; ?> to quick links"></button>
        </div>
      <?php
        }
      }
      ?>
      </div>
    </div>
  <?php
  }

  // Cards the member can't use yet but has been told are on their way, with
  // the card's permission followed by -coming-soon. Several cards share a
  // permission, so each card is only listed once.
  $coming_soon_cards = [];
  foreach ($cards as $category_cards) {
    foreach ($category_cards as $card) {
      if (array_key_exists('card-permission', $card)
          && !current_user_has_permission($card['card-permission'])
          && current_user_has_permission($card['card-permission'] . '-coming-soon')) {
        $coming_soon_cards[$card['name']] = $card;
      }
    }
  }

  if (!empty($coming_soon_cards)) {
  ?>
    <div class="category">
      <h2>Coming soon…</h2>
      <div class="cards">
      <?php
      foreach ($coming_soon_cards as $card) {
      ?>
        <div class="card <?php echo $card['name']; ?> coming-soon">
          <div class="hero" aria-hidden="true">
          </div>
          <hgroup>
            <h3><?php echo htmlspecialchars($card['title']); ?></h3>
            <?php
            if ($card['subtitle']) {
            ?>
            <h4><?php echo htmlspecialchars($card['subtitle']); ?></h4>
            <?php
            }
            ?>
          </hgroup>
        </div>
      <?php
      }
      ?>
      </div>
    </div>
  <?php
  }
  ?>
</nav>

<script>
  const badgeNo = '<?php echo get_current_user_badge_no(); ?>';
  function updateTime() {
    const utc = new Date();
    const $times = [...document.getElementsByClassName('time')].map($time => $time.getElementsByClassName('hero')[0]);
    for (let $time of $times) {
      const newTime = utc.toLocaleString('en-GB', { timeZone: '<?php echo TIMEZONE; ?>', hour: 'numeric', minute: 'numeric', hour12: false });
      if (newTime != $time.innerText) {
        // Only update the time if it has changed.
        // More importantly than an optimization not to change the DOM, it
        // prevents screen readers from announcing the time every 10 seconds.
        $time.innerText = newTime;
      }
    }
    setTimeout(updateTime, 10000);
  }
  updateTime();

  function handleCardClick(e) {
    if (navigator.sendBeacon) {
      const $card = e.target.closest('.card');
      const cardName = [...$card.classList].find(c => c != 'card');
      navigator.sendBeacon('/analytics?event=card-click&data=' + cardName);
    }
  }

  for (let $card of [...document.querySelectorAll('.card a')]) {
    $card.addEventListener('click', handleCardClick);
  }

  const favOrder = [
    'time',
    'chat',
    'upload-photo',
    'stream',
    'maps',
    'benefits',
    'guide',
    'times',
    'signups',
    'newsletter',
    'tours',
    'itinerary',
    'readme',
    'gallery',
    'upload-presentation',
    'business-meeting',
    'accessibility',
    'childcare',
    'volunteer',
    'website',
    'coc',
    'help',
  ];

  function cardName($card) {
    return $card.className.split(' ')[1];
  }

  function toggleFav(target) {
    target.classList.toggle('faved');

    const $card = target.closest('.card');
    const title = $card.getElementsByTagName('h3')[0].innerText;
    const mode = target.classList.contains('faved') ? 'added' : 'removed';
    if (mode == 'added') {
      target.ariaLabel = 'Remove ' + title + ' from quick links';
      const $cards = [...$favsList.getElementsByClassName('card')];
      const $nextCard = $cards.find($c => {
        return favOrder.indexOf(cardName($c)) > favOrder.indexOf(cardName($card));
      });
      const $newCard = $card.cloneNode(true);
      if ($nextCard) {
        $favsList.insertBefore($newCard, $nextCard);
      } else {
        $favsList.appendChild($newCard);
      }
      $newCard.querySelector('a').addEventListener('click', handleCardClick);
      $newCard.getElementsByClassName('fav')[0].addEventListener('click', handleFavClick);
    } else {
      target.ariaLabel = 'Removed';
      // Coming soon cards share the name class but can't be quick links
      const cardSelector = '.card:not(.coming-soon).' + cardName($card);
      const $favedCard = $favsList.querySelector(cardSelector);
      $favsList.removeChild($favedCard);
      const $normalCardFav = document.querySelector(cardSelector).getElementsByClassName('fav')[0];
      $normalCardFav.classList.remove('faved');
      $normalCardFav.ariaLabel = 'Add ' + title + ' to quick links';
    }
    return { mode: mode, card: cardName($card) };
  }

  function handleFavClick(e) {
      if (e.x !== 0 || e.y !== 0) {
        e.target.blur();
      }
      const result = toggleFav(e.target);
      preferences[result.card] = result.mode === 'added';
      document.cookie = 'favs_' + badgeNo + '=' + encodeURIComponent(JSON.stringify(preferences)) + '; path=/;SameSite=Strict;max-age=31560000000;';
      if (navigator.sendBeacon) {
        navigator.sendBeacon('/analytics?event=favs&data=' + (result.mode == 'added' ? '%2B' : '-') + result.card);
      }
  }

  const $favsList = document.getElementById('favs').getElementsByClassName('cards')[0];
  const $favs = [...document.querySelectorAll('.card:not(.coming-soon) .fav')];
  // Card name => whether the member explicitly added (true) or removed (false)
  // it. Defaults only apply to cards the member hasn't chosen either way for,
  // so cards added to the defaults later still reach returning members.
  let preferences = {};
<?php
  $faved_cards = [];
  if (current_user_has_permission('online')) {
    $faved_cards = ['chat', 'upload-photo', 'newsletter', 'guide', 'signups', 'stream'];
  } else {
    $faved_cards = ['chat', 'newsletter', 'maps', 'guide', 'signups'];
  }
  if (current_user_has_permission('programme-participant')) {
    $faved_cards[] = 'itinerary';
  }
  if (current_user_has_permission('childcare')) {
    $faved_cards[] = 'childcare';
  }
  if (current_user_has_permission('see-business-meeting')) {
    $faved_cards[] = 'business-meeting';
  }
  echo "const defaultFavs = " . json_encode($faved_cards) . ";\n";
?>

  const favsCookie = document.cookie.split('; ').find(c => c.startsWith('favs_' + badgeNo + '='));
  if (favsCookie) {
    const cookieValue = decodeURIComponent(favsCookie.split('=')[1]);
    try {
      if (cookieValue.startsWith('{')) {
        preferences = JSON.parse(cookieValue);
      } else {
        // Legacy format: the comma separated list of cards shown, so
        // anything missing from it was removed
        const shown = cookieValue.split(',').filter(Boolean);
        for (const card of defaultFavs) {
          preferences[card] = shown.includes(card);
        }
        for (const card of shown) {
          preferences[card] = true;
        }
      }
    } catch (e) {
      preferences = {};
    }
  }

  const favedCards = defaultFavs
    .filter(card => preferences[card] !== false)
    .concat(Object.keys(preferences).filter(card => preferences[card] === true));
  for (let $fav of $favs) {
    $fav.addEventListener('click', handleFavClick);
    if (favedCards.includes(cardName($fav.closest('.card')))) {
      toggleFav($fav);
    }
  }
</script>

<?php
render_footer();
?>