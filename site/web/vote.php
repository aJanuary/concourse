<?php
require_once('../includes/config.php');
require_once('../includes/session_auth.php');
require_once('../includes/template.php');

if (!current_user_has_permission('see-vote')) {
  header('Location: /');
  exit;
}

$doc_vote_open = strtotime('2024-03-29 12:00');
$doc_vote_close = strtotime('2024-04-01 11:00');
$bsfa_vote_close = strtotime('2024-03-30 12:00');

$name = get_current_user_name();
$badge_no = get_current_user_badge_no();

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
<h3>BSFA Awards</h3>
  <p>The BSFA Awards are voted on by members of the British Science Fiction Association and the Eastercon. Vote by noon on Saturday online (there are no paper ballots). The winners will be announced on Saturday at the BSFA Awards Ceremony.</p>
  <?php if (time() > $bsfa_vote_close) { ?>
  <p><em>Voting has closed.</em></p>
  <p><span class="button disabled">Vote for the BSFA Awards</a></p>
  <?php } else { ?>
    <p><a target="_blank" class="button" href="https://docs.google.com/forms/d/e/1FAIpQLSdJuDiyff79D0J-dnZo4M3iffL2jhDcFNiqJv07AV_O_rryQQ/viewform?usp=pp_url&entry.1413306060=<?php echo $name; ?>&entry.1071906679=<?php echo $badge_no; ?>&entry.1519493446=Yes">Vote in the BSFA Awards</a></p>
  <?php } ?>
  <h3>Doc Weir</h3>
  <p>The Doc Weir Award celebrates the unsung heroes in fandom, those who do the work behind the scenes every year. You can vote by clicking the button below, or find Claire Brialey at the fan funds table in the Dealers' room for a paper ballot.</p>
  <?php if (time() < $doc_vote_open) { ?>
  <p><em>Voting will open on <?php echo date('l jS F ga', $doc_vote_open); ?></em>.</p>
  <p><span class="button disabled">Vote for the Doc Weir Award</a></p>
  <?php } else if (time() > $doc_vote_close) { ?>
  <p><em>Voting has closed.</em></p>
  <p><span class="button disabled">Vote for the Doc Weir Award</a></p>
  <?php } else { ?>
  <p><a target="_blank" class="button" href="https://docs.google.com/forms/d/e/1FAIpQLScnW-OmD5-2ZrJxK32j7CRt3M9VicLp6xbKxE82OqX_MXbC2w/viewform?usp=pp_url&entry.1912252717=<?php echo $name; ?>&entry.1345035588=<?php echo $badge_no; ?>">Vote for the Doc Weir Award</a></p>
  <?php } ?>
  <p>Do you know someone in fandom who helps make things happen, contributing time, effort, ideas and support - often behind the scenes? That might mean that they&apos;ve already won the Doc Weir Award (you can see a list of winners, and what they&apos;ve done, on <a href="https://efanzines.com/DocWeir/">efanzines</a>). But it might mean that you just think they must have won, until you check.</p>
  <p>Named in memory of Dr Arthur Rose ‘Doc&apos; Weir and first presented in 1963, the Award offers some recognition for the community activities that can seem to go unnoticed. It&apos;s been won by authors, booksellers and many fans: fanzine editors, convention runners, club organisers, and other people who just do stuff; winners have included specialists who contribute in a particular role at every opportunity, people who help out all over the place in a variety of ways, and most points in between. It&apos;s not necessarily an award for conrunning in general or Eastercon-running in particular, although it often has been.</p>
  <p>As an on-site or virtual attending member of Levitation, you can vote from Friday 29 March until 11 am on Monday 1 April. (Only one vote per member in that period will count towards this year&apos;s award.) Votes can be cast through the member portal by clicking the button above; if you&apos;re there in person and have a question or prefer a paper ballot form, come to find me at the fan funds table in the dealers&apos; room. The 2024 winner will be announced at the closing ceremony.</p>
  <p><em>- Claire Brialey (2023 winner and current administrator)</em></p>
  <h3>Fan Funds</h3>
  <p>Fan Funds are an opportunity to send fans to conventions they might not otherwise be able to attend.</p>
  <ul>
    <li><strong><a href="https://taff.org.uk/vote.php">Trans-Atlantic Fan Fund</a></strong> which sends fans between Europe and North America.<br><a href="https://taff.org.uk/vote.php" class="button">Vote in the Trans-Atlantic Fan Fund</a>
    <li><strong><a href="https://taff.org.uk/ballots/guff2024.pdf">Going Under Fan Fund</a></strong> which send fans between Europe and Oceania.<br><a href="https://airtable.com/appW5ZzJtRNaTY1i4/pagbhUagYedO5QOWC/form" class="button">Vote in the Going Under Fan Fund</a>
    <li><strong><a href="https://forms.gle/TaTU4e5ajnkSmdMZ9">European Fan Fund</a></strong> which enables fans to travel within Europe to attend the Eurocon.<br><a href="https://forms.gle/TaTU4e5ajnkSmdMZ9" class="button">Vote in the European Fan Fund</a>    
  </ul>
</article>

<?php
render_footer();
?>