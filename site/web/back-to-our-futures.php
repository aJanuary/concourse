<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-btof');

render_header(
  'Back to Our Futures',
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
  <p>Are you missing Glasgow 2024, a Worldcon for Our Futures already?<br>Are you intending to get around to watching all the fabulous online programme but haven&apos;t quite yet?<br>Would you like to meet up online with some of the friends you made at the con?<br>Then this is the exact event for you!</p>
  <p>Glasgow 2024, Back to Our Futures will be an online catchup convention and party, held from Friday 1<sup>st</sup> &ndash; Sunday 3<sup>rd</sup> November 2024. It&apos;s exclusively for Glasgow 2024 attendees aged 16 or over, and is completely free.</p>
  <p>The programme will be the Glasgow 2024 replay programme accessible via the <a href="https://portal.glasgow2024.org/stream">Stream and replay page</a>. There will also be a Discord server for you to hang out, chat and discuss the programme in, and some video social space where we hope some of you will run parties and meetups.</p>
  <p>The team behind this event are Claire Brialey, John Coxon, Andrew January, Emily January, Meg MacDonald, and Alison Scott.</p>
  <p>The team would like you to indicate if you&apos;re likely to attend (so there is a rough idea of numbers) by filling in the <a href="<?php echo $url; ?>" target="_blank">RSVP form</a>.</p>
  <p>Currently the event is uncapped in terms of potential attendees but in the spirit of being able to deliver the best cosy catch-up con possible a cap may be applied if there is exceptionally high uptake.</p>
  <p>Glasgow 2024, A Worldcon for Our Futures supports this event, however it is being run by an independent group who are excited to go back to Our Futures with you. </p>
  <p><a href="<?php echo $url; ?>" class="button" target="_blank">RSVP to register your interest</a></p>
</article>
<?php
render_footer();
?>