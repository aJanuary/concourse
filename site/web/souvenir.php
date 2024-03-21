<?php
require_once('../includes/config.php');
require_once('../includes/session_auth.php');
require_once('../includes/template.php');

if (!current_user_has_permission('see-souvenir')) {
  header('Location: /');
  exit;
}

$magic_link = db_get_magic_link($_COOKIE['session']);

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Souvenir Book</h3>
  <p>Our Guests of Honour have kindly donated videos, essays and short stories that we have compiled into a souvenir book.</p>
  <p>
    <a class="button" href="/resources/souvenir/levitation2024-souvenir.epub">epub</a>
    <a class="button" href="/resources/souvenir/levitation2024-souvenir.pdf">pdf</a>
  </p>
</article>

<?php
render_footer();
?>