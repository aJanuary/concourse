<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

if (!current_user_has_permission('see-participant-guides')) {
  header('Location: /');
  exit;
}

$magic_link = db_get_magic_link($_COOKIE['session']);

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Participant Guides</h3>
  <ul>
    <li><a href="https://eastercon2024.co.uk/participant-guides/guide-for-panellists/">Panellists guide</a></li>
    <li><a href="https://eastercon2024.co.uk/participant-guides/moderator-guide/">Moderator guide</a></li>
    <li><a href="https://eastercon2024.co.uk/participant-guides/presenters-guide/">Presenters guide</a></li>
    <li><a href="https://eastercon2024.co.uk/participant-guides/microphone-technique/">Microphone technique</a></li>
    <li><a href="https://zoom.us/j/93887886660?pwd=M2VzTUM1M2pyRitSaHpBZ0JvQlFBUT09">Zoom link for virtual participants</a></li>
</article>

<?php
render_footer();
?>