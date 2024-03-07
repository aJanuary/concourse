<?php
require_once('../../includes/config.php');
require_once('../../includes/session_auth.php');
require_once('../../includes/template.php');

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Doc Weir</h3>
  <p>The Doc Weir Award celebrates the unsung heroes in fandom, those who do the work behind the scenes every year. You can vote for your chosen candidate online, or fill in the form and putting in the box at Ops Help Desk by 10:30 am on Monday.</p>
  <p><a target="_blank" class="button" href="https://forms.gle/7xDLsFmHQJpve7V16">Vote for the Doc Weir Award</a></p>

  <h3>BSFA Awards</h3>
  <p>The BSFA Awards are voted on by members of the British Science Fiction Association and the Eastercon. Vote by noon on Saturday online (there are no paper ballots). The winners will be announced on Saturday at the BSFA Awards Ceremony.</p>
  <p><a target="_blank" class="button" href="javascript:alert('todo')">Vote in the BSFA Awards</a></p>
</article>

<?php
render_footer();
?>