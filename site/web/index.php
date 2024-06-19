<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header();

$cards = [
  [
    'name' => 'time',
    'title' => 'Convention time',
    'subtitle' => 'BST',
    'description' => 'Convert between convention time (BST) and your local timezone.',
    'link' => 'https://dateful.com/convert/british-summer-time-bst',
    'card-permission' => 'see-time',
  ], [
    'name' => 'guide',
    'title' => 'Programme guide',
    'subtitle' => 'ConClár',
    'description' => 'See what\'s on where and when. Bookmark items to make sure you don\'t miss them.',
    'link' => '/under-construction',
    'card-permission' => 'see-guide',
  ], [
    'name' => 'maps',
    'title' => 'Maps',
    'subtitle' => '',
    'description' => 'Find your way around the convention venues.',
    'link' => '/under-construction',
    'card-permission' => 'see-maps',
  ], [
    'name' => 'readme',
    'title' => 'Pocket guide',
    'subtitle' => 'Readme',
    'description' => 'Find out all the key information you need to know for ' . CON_SHORT_NAME . '.',
    'link' => '/under-construction',
    'card-permission' => 'see-readme',
  ], [
    'name' => 'stream',
    'title' => 'Stream and replay',
    'subtitle' => 'RingCentral Events',
    'description' => 'Watch the live streams of programme items, or watch the recorded videos afterwards.',
    'link' => '/stream',
    'card-permission' => 'see-rce',
  ], [
    'name' => 'chat',
    'title' => 'Chat online',
    'subtitle' => 'Discord',
    'description' => 'Chat with other members online in Discord. Talk about panel items, your fannish interests, and catch up with friends.',
    'link' => '/chat',
    'card-permission' => 'see-discord',
  ], [
    'name' => 'signups',
    'title' => 'Sign-ups',
    'subtitle' => '',
    'description' => 'Sign up for workshops, table talks and more.',
    'link' => '/under-construction',
    'card-permission' => 'see-signups',
  ], [
    'name' => 'newsletter',
    'title' => 'Newsletter',
    'subtitle' => 'Get the latest news.',
    'link' => '/under-construction',
    'card-permission' => 'see-help',
  ], [
    'name' => 'volunteer',
    'title' => 'Volunteer',
    'subtitle' => '',
    'description' => 'Find out how you can help out at ' . CON_NAME . '.',
    'link' => '/under-construction',
    'card-permission' => 'see-website',
  ], [
    'name' => 'gallery',
    'title' => 'Artist&apos;s alley',
    'subtitle' => '',
    'description' => 'View a gallary of our exhibitor\'s art.',
    'link' => '/under-construction',
    'card-permission' => 'see-gallery',
  ], [
    'name' => 'hugos',
    'title' => 'Hugos',
    'subtitle' => 'Voting',
    'description' => 'Vote for the Hugos.',
    'link' => 'https://glasgow2024.org/hugo-awards/hugo-awards-final-ballot/',
    'card-permission' => 'see-hugos',
  ], [
    'name' => 'site-selection',
    'title' => 'Site selection',
    'subtitle' => 'Voting',
    'description' => 'Vote in the site selection for the 2026 World Science Fiction Convention.',
    'link' => '/under-construction',
    'card-permission' => 'see-site-selection',
  ], [
    'name' => 'itinerary',
    'title' => 'Your participant schedule',
    'subtitle' => 'Planorama',
    'description' => 'Find the time and room for the programme items you are on. Get links to join remotely if you are participating online.',
    'link' => '/under-construction',
    'card-permission' => 'see-itinerary',
    'link-permission' => 'see-itinerary-other',
  ], [
    'name' => 'childcare',
    'title' => 'Childcare',
    'subtitle' => '',
    'description' => 'Opening hours and location for childcare.',
    'link' => 'https://glasgow2024.org/for-members/childcare/',
    'card-permission' => 'has-childcare',
    'link-permission' => 'see-childcare',
  ], [
    'name' => 'help',
    'title' => 'Get help',
    'subtitle' => 'Info desk, help desks and contacts',
    'description' => 'If you have a problem or question, you can reach out to us in-person, or online through Discord and e-mail.',
    'link' => '/under-construction',
    'card-permission' => 'see-help',
  ], [
    'name' => 'website',
    'title' => 'Website',
    'subtitle' => '',
    'description' => 'Find out the latest news and other information about ' . CON_NAME . '.',
    'link' => 'https://glasgow2024.org/',
    'card-permission' => 'see-website',
  ], [
    'name' => 'manage-roles',
    'title' => 'Manage roles',
    'subtitle' => 'Admin',
    'description' => 'Manage the roles of members.',
    'link' => '/admin/roles',
    'link-permission' => 'manage-roles',
  ], [
    'name' => 'manage-discord',
    'title' => 'Discord ids',
    'subtitle' => 'Admin',
    'description' => 'Manage the Discord ids of members.',
    'link' => '/admin/discord',
    'link-permission' => 'manage-discord-ids',
  ], [
    'name' => 'manage-programme',
    'title' => 'Manage programme',
    'subtitle' => 'Admin',
    'description' => 'Manage the programme items.',
    'link' => '/admin/programme/list',
    'link-permission' => 'manage-programme',
  ]
];
?>


<div class="cards">
<?php
foreach ($cards as $card) {
  if (array_key_exists('card-permission', $card) && current_user_has_permission($card['card-permission'])) {
?>
  <a href="<?php echo $card['link']; ?>" class="card <?php echo $card['name']; ?>">
    <div class="hero"></div>
    <h3><?php echo $card['title']; ?></h3>
    <h4><?php echo $card['subtitle']; ?></h4>
    <p><?php echo $card['description']; ?></p>
  </a>
<?php
  }
}
?>
</div>

<?php
$others = array_filter($cards, function($card) {
  return (array_key_exists('link-permission', $card) && current_user_has_permission($card['link-permission'])) &&
         (!array_key_exists('card-permission', $card) || !current_user_has_permission($card['card-permission']));
});

if (!empty($others)) {
?>
<div class="other">
  <h2>Other</h2>
  <ul>
<?php
  foreach ($others as $link) {
?>
    <li><a href="<?php echo $link['link']; ?>"><?php echo $link['title']; ?></a></li>
<?php
  }
}
?>
  </ul>
</div>

<script>
  let $times = [...document.getElementsByClassName('time')].map($time => $time.getElementsByClassName('hero')[0]);
  function updateTime() {
    let utc = new Date();
    for (let $time of $times) {
      $time.innerText = utc.toLocaleString('en-GB', { timeZone: '<?php echo TIMEZONE; ?>', hour: 'numeric', minute: 'numeric', hour12: false });
    }
    setTimeout(updateTime, 10000);
  }
  if ($times.length > 0) {
    updateTime();
  }
</script>

<?php
render_footer();
?>