<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');

check_permission('see-site-selection');

$assignment = db_get_member_site_selection(get_current_user_badge_no());
$just_paid = ($_GET['success'] ?? '') === 'true';
$bids = SITE_SELECTION['bids'];
shuffle($bids);

render_header(
  'Site selection',
  'Vote in site selection for the ' . SITE_SELECTION['year'] . ' Worldcon.',
  ['Home' => '/', 'Site selection']
);
?>
<article>
  <h2>Site selection</h2>
  <p>Voting in site selection lets you have your say in where the <?php echo htmlspecialchars(SITE_SELECTION['ordinal']); ?> Worldcon will be held in <?php echo htmlspecialchars(SITE_SELECTION['year']); ?>.</p>
  <p>To vote, pay the site selection fee of <strong><?php echo htmlspecialchars(SITE_SELECTION['fee']); ?></strong>. This buys you an advance <a href="https://www.wsfs.org/membership/" target="_blank">WSFS membership</a> of the <?php echo htmlspecialchars(SITE_SELECTION['year']); ?> Worldcon, and the fees are passed on to the winning bid.</p>
<?php
if ($just_paid && $assignment !== null) {
?>
  <p class="info" role="status">Thank you for paying the site selection fee. You can now vote.</p>
<?php
}

if ($assignment !== null && $assignment['method'] === 'online') {
?>
  <p><a href="<?php echo htmlspecialchars(site_selection_ballot_url($assignment['token'])); ?>" class="button" target="_blank">Vote in site selection</a></p>
  <p>Don't share this link with anyone: it lets whoever has it vote as you.</p>
<?php
} else if ($assignment !== null) {
?>
  <p class="info">You are voting with a paper ballot.</p>
<?php
} else {
?>
  <p><a href="/site-selection/details" class="button">Pay the site selection fee</a></p>
<?php
}

if ($assignment === null || $assignment['method'] === 'online') {
?>
  <p>Or <a href="<?php echo htmlspecialchars(SITE_SELECTION['paper_ballot_url']); ?>" target="_blank">vote with a paper ballot</a>.</p>
<?php
}
?>
  <h3>The bids</h3>
  <ul>
<?php
foreach ($bids as $bid) {
  $link = '<a href="' . htmlspecialchars($bid['url']) . '" target="_blank">' . htmlspecialchars($bid['name']) . '</a>';
  if ($bid['withdrawn'] ?? false) {
?>
    <li><s><?php echo $link; ?></s> has withdrawn.</li>
<?php
  } else {
?>
    <li><?php echo $link; ?></li>
<?php
  }
}
?>
  </ul>
  <p>Find out more about <a href="<?php echo htmlspecialchars(SITE_SELECTION['info_url']); ?>" target="_blank">how site selection works</a>.</p>
</article>
<?php
if ($just_paid) {
?>
<script>
// So reloading or bookmarking the page doesn't thank them again
const url = new URL(window.location);
url.searchParams.delete('success');
window.history.replaceState({}, document.title, url.pathname + url.search + url.hash);
</script>
<?php
}
render_footer();
?>
