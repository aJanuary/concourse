<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  null,
  'The Member Portal for Glasgow 2024, A Worldcon for Our Futures.',
  null,
  true
);

$cards = [
  'Info' => [
    [
      'name' => 'time',
      'title' => 'Convention time',
      'subtitle' => 'BST',
      'link' => 'https://dateful.com/convert/british-summer-time-bst',
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
      'link' => '/under-construction?page=Convention+guide',
      'card-permission' => 'see-readme',
    ], [
      'name' => 'newsletter',
      'title' => 'Newsletter',
      'subtitle' => '',
      'link' => '/under-construction?page=Newsletter',
      'card-permission' => 'see-help',
    ], [
      'name' => 'childcare',
      'title' => 'Childcare',
      'subtitle' => '',
      'link' => 'https://glasgow2024.org/for-members/childcare/',
      'card-permission' => 'has-childcare',
      'link-permission' => 'see-childcare',
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
      'link' => 'https://glasgow2024.org/',
      'card-permission' => 'see-website',
    ]
  ],
  'Programme' => [
    [
      'name' => 'guide',
      'title' => 'Programme guide',
      'subtitle' => 'ConClár',
      'link' => 'https://ajanuary.com/static/glasgow2024-conclar/',
      'card-permission' => 'see-guide',
    ], [
      'name' => 'stream',
      'title' => 'Stream and replay',
      'subtitle' => 'RingCentral Events',
      'link' => '/stream',
      'card-permission' => 'see-rce',
    ], [
      'name' => 'signups',
      'title' => 'Sign-ups',
      'subtitle' => '',
      'link' => '/under-construction?page=Sign-ups',
      'card-permission' => 'see-signups',
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
      'link' => 'https://glasgow2024.org/for-members/volunteering/',
      'card-permission' => 'see-website',
    ], [
      'name' => 'itinerary',
      'title' => 'Your participant schedule',
      'subtitle' => 'Planorama',
      'link' => '/under-construction?page=Your+participant+schedule',
      'card-permission' => 'see-itinerary',
      'link-permission' => 'see-itinerary-other',
    ]
  ],
  'Other' => [
    [
      'name' => 'gallery',
      'title' => 'Artist&apos;s alley',
      'subtitle' => '',
      'link' => '/under-construction?page=Artist%27s+alley',
      'card-permission' => 'see-gallery',
    ], [
      'name' => 'hugos',
      'title' => 'Hugos',
      'subtitle' => 'Results and stats',
      'link' => 'https://glasgow2024.org/hugo-awards/2024-hugo-award-winners/',
      'card-permission' => 'see-hugos',
    ], [
      'name' => 'site-selection',
      'title' => 'Site selection',
      'subtitle' => 'Voting',
      'link' => 'https://glasgow2024.org/for-members/wsfs-site-selection/',
      'card-permission' => 'see-site-selection',
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
      ?>
        <div class="card <?php echo $card['name']; ?>">
          <div class="hero"
          <?php if (array_key_exists('live-card', $card) && $card['live-card']) { ?>
              aria-label="<?php echo $card['title']; ?>"
          <?php } else { ?>
              aria-hidden="true"
          <?php } ?>
          ></div>
          <hgroup>
            <h3><a href="<?php echo $card['link']; ?>"><?php echo $card['title']; ?></a></h3>
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

  const favOrder = [
    'time',
    'chat',
    'maps',
    'guide',
    'stream',
    'signups',
    'newsletter',
    'itinerary',
    'readme',
    'gallery',
    'hugos',
    'site-selection',
    'childcare',
    'volunteer',
    'website',
    'help',
  ];

  function cardName($card) {
    return $card.className.split(' ')[1];
  }

  function handleFavClick(e) {
      if (e.x !== 0 || e.y !== 0) {
        e.target.blur();
      }
      e.target.classList.toggle('faved');

      const $card = e.target.closest('.card');
      const title = $card.getElementsByTagName('h3')[0].innerText;
      if (e.target.classList.contains('faved')) {
        e.target.ariaLabel = 'Remove ' + title + ' from quick links';
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
        $newCard.getElementsByClassName('fav')[0].addEventListener('click', handleFavClick);
      } else {
        e.target.ariaLabel = 'Add ' + title + ' to quick links';
        const cardSelector = '.' + cardName($card);
        const $favedCard = $favsList.querySelector(cardSelector);
        $favsList.removeChild($favedCard);
        document.querySelector(cardSelector).getElementsByClassName('fav')[0].classList.remove('faved');
      }

      // Set cookie with name of faved cards.
      const favedCards = [...$favsList.getElementsByClassName('card')].map($c => cardName($c));
      document.cookie = 'favs_' + badgeNo + '=' + favedCards.join(',') + '; path=/;SameSite=Strict;max-age=31560000000;';
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
      $faved_cards = ['chat', 'maps', 'guide', 'signups'];
    }
    if (current_user_has_permission('programme-participant')) {
      $faved_cards.push('itinerary');
    }
    if (current_user_has_permission('childcare')) {
      $faved_cards.push('childcare');
    }
    echo "favedCards = " . json_encode($faved_cards) . ";\n";
?>
  }
  for (let $fav of $favs) {
    $fav.addEventListener('click', handleFavClick);
    if (favedCards.includes(cardName($fav.closest('.card')))) {
      $fav.click();
    }
  }
</script>

<?php
render_footer();
?>