<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/business_meeting.php');

check_permission('see-business-meeting');

$join_link = db_get_business_meeting_link(get_current_user_badge_no());
$platform = BUSINESS_MEETING['platform_name'];
$election = business_meeting_mpc_election();
$mpc_state = business_meeting_mpc_state(new DateTime());

function help_email_link() {
  $email = htmlspecialchars(BUSINESS_MEETING['help_email']);
  return '<a href="mailto:' . $email . '">' . $email . '</a>';
}

render_header(
  'Business Meeting',
  'Join the WSFS Business Meeting online.',
  ['Home' => '/', 'Business Meeting']
);
?>
<article>
  <h2>WSFS Business Meeting</h2>
  <p>The <a href="<?php echo htmlspecialchars(BUSINESS_MEETING['info_url']); ?>" target="_blank">WSFS Business Meeting</a> is held online using <a href="<?php echo htmlspecialchars(BUSINESS_MEETING['platform_info_url']); ?>" target="_blank"><?php echo htmlspecialchars($platform); ?></a> for audio, video and voting, and a separate Discord server for chat.<?php if (BUSINESS_MEETING['guide_url']) { ?> Read the <a href="<?php echo htmlspecialchars(BUSINESS_MEETING['guide_url']); ?>" target="_blank">guide to attending</a> before your first session.<?php } ?></p>

  <h3>Audio, video and voting</h3>
<?php
if ($join_link !== null) {
?>
  <p>Join each session on <?php echo htmlspecialchars($platform); ?> with your personal link.</p>
  <p><a href="<?php echo htmlspecialchars($join_link); ?>" target="_blank" class="button">Join <?php echo htmlspecialchars($platform); ?></a></p>
  <p>Don't share this link with anyone: it lets whoever has it speak and vote as you.</p>
  <p>If you have any problems with <?php echo htmlspecialchars($platform); ?>, including logging in, contact <?php echo help_email_link(); ?>.</p>
<?php
} else {
?>
  <div class="warn">
    <img src="/resources/warn.svg" alt="warning" aria-label="warning">
    <p>You don't currently have access to the business meeting. This may be because:</p>
    <ul>
      <li>your membership doesn't include attending the business meeting, or</li>
      <li>you joined recently and we haven't set up your access yet.</li>
    </ul>
<?php
  if (BUSINESS_MEETING['no_link_note'] !== '') {
?>
    <p><?php echo htmlspecialchars(BUSINESS_MEETING['no_link_note']); ?></p>
<?php
  }
?>
    <p>Contact <?php echo help_email_link(); ?> if you think this is a mistake.</p>
  </div>
<?php
}

if ($mpc_state !== null) {
  $opens = business_meeting_format_time(business_meeting_time($election['opens']));
  $closes = business_meeting_format_time(business_meeting_time($election['closes']));
?>
  <h3>Mark Protection Committee election</h3>
  <p>The <a href="https://www.wsfs.org/committees/mark-protection-committee/" target="_blank">Mark Protection Committee</a> registers and protects the World Science Fiction Society's service marks. Members are elected at each year's Business Meeting.</p>
<?php
  if ($join_link === null) {
?>
  <p>You don't currently have access to the MPC election.</p>
<?php
  } else if ($mpc_state === 'before') {
?>
  <p>The election opens on <strong><?php echo htmlspecialchars($opens); ?></strong>.</p>
<?php
  } else if ($mpc_state === 'open') {
?>
  <p>The election is open to everyone attending the business meeting.</p>
  <p><a href="<?php echo htmlspecialchars($election['url']); ?>" target="_blank" class="button">Vote in the MPC election</a></p>
  <p>Vote by <strong><?php echo htmlspecialchars($closes); ?></strong>.</p>
<?php
  } else {
?>
  <p>The election closed on <?php echo htmlspecialchars($closes); ?>.</p>
<?php
  }
}
?>
  <h3>Chat</h3>
  <p>Chat with other attendees during the meeting on the Business Meeting Discord server. It is separate from the convention's Discord server.</p>
<?php
if ($join_link === null) {
?>
  <p>You don't currently have access to the Business Meeting Discord server.</p>
<?php
} else if (DISCORD_BM_INVITE !== '') {
?>
  <p><a href="https://discord.gg/<?php echo htmlspecialchars(rawurlencode(DISCORD_BM_INVITE)); ?>" target="_blank" class="button">Join the Business Meeting Discord</a></p>
<?php
} else {
?>
  <p><strong>The Business Meeting Discord server isn't open yet. Please check back soon.</strong></p>
<?php
}

if (BUSINESS_MEETING['sessions']) {
?>
  <h3>Dates</h3>
  <ul>
<?php
  foreach (BUSINESS_MEETING['sessions'] as $session) {
?>
    <li><?php echo htmlspecialchars(business_meeting_format_time(business_meeting_time($session))); ?></li>
<?php
  }
?>
  </ul>
<?php
  if (BUSINESS_MEETING['duration'] !== '') {
?>
  <p><?php echo htmlspecialchars(BUSINESS_MEETING['duration']); ?></p>
<?php
  }
}
?>
</article>
<?php
render_footer();
?>
