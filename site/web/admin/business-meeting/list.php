<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/business_meeting.php');

check_permission('manage-business-meeting');

$messages = [
  'saved' => 'Saved the link for badge %s.',
  'deleted' => 'Deleted the link for badge %s.',
];
$message = isset($messages[$_GET['done'] ?? '']) ? sprintf($messages[$_GET['done']], (string) ($_GET['badge_no'] ?? '')) : null;

$links = db_get_business_meeting_links();

render_header(
  'Manage business meeting',
  'Manage business meeting join links.',
  ['Home' => '/', 'Manage business meeting']
);
?>
  <article>
    <h2>Manage business meeting</h2>
<?php
if ($message !== null) {
?>
    <p class="info"><?php echo htmlspecialchars($message); ?></p>
<?php
}
?>
    <p>Members with the <code>see-business-meeting</code> permission can join the business meeting with the <?php echo htmlspecialchars(BUSINESS_MEETING['platform_name']); ?> join link for their badge number.</p>
    <ul>
      <li><a href="/admin/business-meeting/edit">Add a link...</a></li>
      <li><a href="/admin/business-meeting/upload">Upload links...</a></li>
    </ul>
    <p><?php echo count($links) === 1 ? '1 member has' : count($links) . ' members have'; ?> a join link. Names are only shown for members who have logged in.</p>
<?php
if ($links) {
?>
    <table>
      <thead>
        <tr><th>Badge number</th><th>Name</th><th>Join link</th><th>Updated</th></tr>
      </thead>
      <tbody>
<?php
  foreach ($links as $link) {
?>
        <tr>
          <td><a aria-label="Edit the link for badge <?php echo htmlspecialchars($link['badge_no']); ?>" href="/admin/business-meeting/edit?badge_no=<?php echo urlencode($link['badge_no']); ?>"><?php echo htmlspecialchars($link['badge_no']); ?></a></td>
          <td><?php echo htmlspecialchars($link['name'] ?? ''); ?></td>
          <td class="join-link"><details><summary>Show</summary><code><?php echo htmlspecialchars($link['join_link']); ?></code></details></td>
          <td><?php echo htmlspecialchars(business_meeting_format_time((new DateTime('@' . $link['updated_at']))->setTimezone(new DateTimeZone(TIMEZONE)))); ?></td>
        </tr>
<?php
  }
?>
      </tbody>
    </table>
<?php
}
?>
  </article>
<?php
render_footer();
?>
