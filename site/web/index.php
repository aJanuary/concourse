<?php
require_once('../includes/config.php');
require_once('../includes/session_auth.php');
require_once('../includes/template.php');

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
    <p>Find out all the key information you need to know for Levitation 2024.</p>
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

<?php
  if (current_user_has_permission('see-hopin')) {
?>
  <a href="/stream" class="card stream">
    <div class="hero"></div>
    <h3>Stream and catch-up</h3>
    <h4>RingCentral Events</h4>
    <p>Watch the live streams of programme items, or watch the recorded videos afterwards.</p>
  </a>
<?php
  }
?>

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

<?php
  if (current_user_has_permission('see-participant-guides')) {
?>
  <a href="https://eastercon2024.co.uk/participant-guides/" class="card partguide">
    <div class="hero"></div>
    <h3>Participant Guides</h3>
    <h4>For people on programme</h4>
    <p>Guides for panellists, moderators and speakers. Please make sure you've read the appropriate guides before your item.</p>
  </a>
<?php
  }
?>

  <a href="https://eastercon2024.co.uk" class="card website">
    <div class="hero"></div>
    <h3>Website</h3>
    <h4>&nbsp;</h4>
    <p>Find out the latest news and other information about Levitation 2024.</p>
  </a>

<?php
  if (current_user_has_permission('manage-discord-ids')) {
?>
  <a href="/admin/discord" class="card discord-mod">
    <div class="hero"></div>
    <h3>Discord ids</h3>
    <h4>Admin area</h4>
    <p>See what member's discord ids are.</p>
  </a>
<?php
  }
?>
</div>

<?php
render_footer();
?>