<?php
$_REQUEST['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Switch user',
  'Switch to a different user',
  ['Home' => '/', 'Switch user']
);

?>
<article>
  <h2>Switch user</h2>
  
<?php
$members = db_get_members_owned_by_same_user(get_current_user_badge_no());
if (count($members) <= 1) {
    echo "<p>Your email is not associated with any other members.</p>";
} else {
    echo "<p>Please select which user you would like to switch to:</p>";
    echo "<ul>";
    foreach ($members as $member) {
        $badge_no = $member['badge_no'];
        $name = $member['name'];
        $link = create_secure_link('/login', [
            'phase' => 'selected-user',
            'badge_no' => $badge_no,
            'redirect' => $_REQUEST['redirect'] ?? '/',
        ]);
        echo "<li><a href=\"" . htmlspecialchars($link) . "\">" . htmlspecialchars($name) . "</a></li>";
    }
    echo "</ul>";
}
?>
  
</article>
<?php
render_footer();
?>