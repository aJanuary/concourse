<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = false;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Switch member',
  'Switch to a different member',
  ['Home' => '/', 'Switch member']
);

?>
<article>
  <h2>Switch member</h2>
  
<?php
$members = db_get_members_owned_by_same_user(get_current_user_badge_no());
if (count($members) <= 1) {
    echo "<p>Your email is not associated with any other members.</p>";
} else {
    echo "<p>Select which member you would like to switch to:</p>";
    render_user_select($members);
}
?>
  
</article>
<?php
render_footer();
?>