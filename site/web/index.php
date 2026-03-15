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
      'subtitle' => 'BST',
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
      'link' => 'https://eastercon2026.org/about/code-of-conduct',
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
      'link' => 'https://eastercon2026.org/',
      'card-permission' => 'see-website',
    ]
  ],
  'Programme' => [
    [
      'name' => 'guide',
      'title' => 'Programme guide',
      'subtitle' => 'ConClár',
      'link' => current_user_has_permission('see-guide-redirect') ? 'https://guide.eastercon2026.org' : '/programme-guide',
      'card-permission' => 'see-guide',
    ], [
      'name' => 'stream',
      'title' => 'Stream and replay',
      'subtitle' => 'YouTube',
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
      'link' => 'https://eastercon2026.org/volunteering',
      'card-permission' => 'see-volunteer',
    ], [
      'name' => 'fan-funds',
      'title' => 'Fan funds',
      'subtitle' => 'Vote',
      'link' => '/fan-funds',
      'card-permission' => 'see-fan-funds',
    ], [
      'name' => 'doc-weir',
      'title' => 'Doc Weir',
      'subtitle' => 'Vote',
      'link' => '/doc-weir',
      'card-permission' => 'see-doc-weir',
    ], [
      'name' => 'upload-photo',
      'title' => 'Upload your photo',
      'subtitle' => 'For online members',
      'link' => '/upload-photo',
      'card-permission' => 'see-upload-photo',
    ], [
      'name' => 'itinerary',
      'title' => 'Your participant schedule',
      'subtitle' => 'PlanZ',
      'link' => 'https://participants.eastercon2026.org/MySchedule.php',
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
      'name' => 'artist-info',
      'title' => 'Information for artists',
      'subtitle' => '',
      'link' => '/artist-info',
      'card-permission' => 'see-artist-info',
    ], [
      'name' => 'dealer-info',
      'title' => 'Information for dealers',
      'subtitle' => '',
      'link' => '/dealer-info',
      'card-permission' => 'see-dealer-info',
    ], [
      'name' => 'games',
      'title' => 'Games',
      'subtitle' => 'Sign-up for RPGs',
      'link' => '/games',
      'card-permission' => 'see-games',
    ]
  ],
  'Other' => [
    [
      'name' => 'gallery',
      'title' => 'Art show',
      'subtitle' => '',
      'link' => 'https://eastercon2026.org/art-show',
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
      'name' => 'manage-login-sessions',
      'title' => 'Manage login sessions',
      'subtitle' => 'Admin',
      'link' => '/admin/login-sessions',
      'card-permission' => 'manage-login-sessions',
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
    'artist-info',
    'dealer-info',
    'games',
    'upload-presentation',
    'fan-funds',
    'doc-weir',
    'accessibility',
    'childcare',
    'volunteer',
    'website',
    'code-of-conduct',
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
      const cardSelector = '.' + cardName($card);
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
      // Set cookie with name of faved cards.
      const favedCards = [...$favsList.getElementsByClassName('card')].map($c => cardName($c));
      document.cookie = 'favs_' + badgeNo + '=' + favedCards.join(',') + '; path=/;SameSite=Strict;max-age=31560000000;';
      if (navigator.sendBeacon) {
        navigator.sendBeacon('/analytics?event=favs&data=' + (result.mode == 'added' ? '%2B' : '-') + result.card);
      }
  }

  const $favsList = document.getElementById('favs').getElementsByClassName('cards')[0];
  const $favs = [...document.getElementsByClassName('fav')];
  let favedCards;
  if (document.cookie.includes('favs_' + badgeNo)) {
    favedCards = document.cookie.split('; ').find(c => c.startsWith('favs_' + badgeNo)).split('=')[1].split(',');
  } else {
<?php
    $faved_cards = [];
    if (current_user_has_permission('online')) {
      $faved_cards = ['chat', 'newsletter', 'guide', 'signups', 'stream'];
    } else {
      $faved_cards = ['chat', 'newsletter', 'maps', 'guide', 'signups'];
    }
    if (current_user_has_permission('programme-participant')) {
      $faved_cards.push('itinerary');
      $faved_cards.push('participant-guides');
    }
    echo "favedCards = " . json_encode($faved_cards) . ";\n";
?>
  }
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