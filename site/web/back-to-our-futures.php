<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-btof');

render_header(
  'Back to Our Furures',
  'A catch-up con for Glasgow 2024, A Worldcon for Our Futures.',
  ['Home' => '/', 'Back to Our Futures']
);

$url = "https://docs.google.com/forms/d/e/1FAIpQLSe6wkkzcgvXH4-wVdE95SBFMrMdtteyKD1C0o2_mYc1_g_T7A/viewform";
if (!is_anonymous()) {
  $badge_no = get_current_user_badge_no();
  $member = db_get_member($badge_no);
  $url = $url . "?usp=pp_url&entry.1159000747=" . urlencode($member['email']);
}
?>
<article>
  <h2>Back to Our Futures</h2>
  <p>Are you missing Glasgow 2024, a Worldcon for Our Futures already? Are you intending to get round to watching all the fabulous online programme but haven&apos;t quite yet? Would you like to meet up online with some of the friends you made at the con?</p>
  <p>Glasgow 2024, Back to Our Futures will be an online catchup convention and party, held from Friday 1<sup>st</sup> &ndash; Sunday 3<sup>rd</sup> November 2024. It&apos;s exclusively for Glasgow 2024 attendees aged 16 or over, and is completely free.</p>
  <p>The programme will be the Glasgow 2024 <a href="/stream">replay programme</a>, and we&apos;ll also have a Discord server for you to hang out, chat and discuss the programme in, and some video social space where we hope some of you will run parties and meetups.</p> 
  <p>The team behind it is Claire Brialey, John Coxon, Andrew January, Emily January, Meg MacDonald and Alison Scott.</p>
  <p>We&apos;d like you to indicate if you&apos;re likely to attend (so we have a rough idea of numbers) by filling out the form linked below. We hope to host everyone who wants to come, but we will cap numbers if it&apos;s looking too huge.</p>
  <p><a href="<?php echo $url; ?>" class="button" target="_blank">Register your interest</a></p>
</article>
<?php
render_footer();
?>