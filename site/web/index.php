<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header();
?>

<div class="cards">
<?php
  if (current_user_has_permission('see-readme')) {
?>
  <a href="https://eastercon2024.co.uk/readme/" class="card readme">
    <div class="hero"></div>
    <h3>Readme</h3>
    <h4>&nbsp;</h4>
    <p>Find out all the key information you need to know for <?php echo CON_SHORT_NAME; ?>.</p>
  </a>
<?php
  }
?>

<?php
  if (current_user_has_permission('see-guide')) {
?>
  <a href="https://guide.eastercon2024.co.uk/" class="card guide">
    <div class="hero"></div>
    <h3>Programme guide</h3>
    <h4>ConCl&aacute;r</h4>
    <p>See what's on where and when. Bookmark items to make sure you don't miss them.</p>
  </a>
<?php
  }
?>

  <a href="/stream" class="card stream">
    <div class="hero"></div>
    <h3>Stream and catch-up</h3>
    <h4>RingCentral Events</h4>
    <p>Watch the live streams of programme items, or watch the recorded videos afterwards.</p>
  </a>

<?php
  if (current_user_has_permission('see-discord')) {
?>
  <a href="/chat" class="card chat">
    <div class="hero"></div>
    <h3>Chat online</h3>
    <h4>Discord</h4>
    <p>Chat with other members online in Discord. Talk about panel items, your fannish interests, and catch up with friends.</p>
  </a>
<?php
  }
?>

<?php
  if (current_user_has_permission('see-newsletter')) {
?>
  <a href="/newsletter" class="card newsletter">
    <div class="hero"></div>
    <h3>Newsletter</h3>
    <h4></h4>
    <p>Find out the latest announcement and convention gossip.</p>
  </a>
<?php
  }
?>

<?php
  if (current_user_has_permission('see-souvenir')) {
?>
  <a href="/souvenir" class="card souvenir">
    <div class="hero"></div>
    <h3>Souvenir Book</h3>
    <h4>epub and pdf</h4>
    <p>Videos, articles and short stories from our Guests of Honour, compiled into an e-book.</p>
  </a>
<?php
  }
?>

<?php
  if (current_user_has_permission('see-vote')) {
?>
  <a href="/vote" class="card vote">
    <div class="hero"></div>
    <h3>Vote</h3>
    <h4>Doc Weir and BSFA</h4>
    <p>Vote for the Doc Weir award and the BSFA awards.</p>
  </a>
<?php
  }
?>

<a href="https://app.collectionpot.com/pot/3064884" class="card donate">
  <div class="hero"></div>
  <h3>Collection for staff</h3>
  <h4></h4>
  <p>To show our appreciation of the staff of the Telford International Centre for their dedication during Levitation.</p>
</a>

<?php
  if (current_user_has_permission('see-participant-guides')) {
?>
  <a href="/participants" class="card partguide">
    <div class="hero"></div>
    <h3>Participant Guides</h3>
    <h4>Guides and Zoom Link</h4>
    <p>Guides for panellists, moderators and speakers. Please make sure you've read the appropriate guides before your item.</p>
  </a>
<?php
  }
?>

  <a href="https://glasgow2024.org/" class="card website">
    <div class="hero"></div>
    <h3>Website</h3>
    <h4>&nbsp;</h4>
    <p>Find out the latest news and other information about <?php echo CON_NAME; ?>.</p>
  </a>
</div>

<?php
$has_manage_roles = current_user_has_permission('manage-roles');
$has_manage_discord = current_user_has_permission('manage-discord-ids');
$has_other = $has_manage_roles || $has_manage_discord;
if ($has_other) {
?>
<div class="other">
  <h2>Other</h2>
  <ul>
<?php
  if ($has_manage_roles) {
?>
    <li><a href="/admin/roles">Manage roles</a></li>
<?php
  }
?>
<?php
  if ($has_manage_discord) {
?>
    <li><a href="/admin/discord">Discord ids</a></li>
<?php
  }
?>
<?php
}
?>

</div>

<?php
render_footer();
?>