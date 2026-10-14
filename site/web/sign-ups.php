<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/signups.php');

render_header(
  'Sign-ups',
  'Sign-up for programme items at ' . CON_NAME . '.',
  ['Home' => '/', 'Sign-ups']
);

function render_signup($signup, $anchor, $member, $now) {
  $state = signup_state($signup, $now);
?>
    <li id="<?php echo htmlspecialchars($anchor); ?>">
      <h5><a href="#<?php echo htmlspecialchars($anchor); ?>"><?php echo htmlspecialchars($signup['title']); ?></a></h5>
<?php
  if ($signup['notes'] !== null) {
?>
      <p><em><?php echo nl2br(htmlspecialchars($signup['notes'])); ?></em></p>
<?php
  }

  $details = [];
  if ($signup['guide_url'] !== null) {
    $details[] = '<a href="' . htmlspecialchars($signup['guide_url']) . '" target="_blank">More information</a>';
  }
  if ($state === 'not-open') {
    $details[] = 'Opens ' . htmlspecialchars(signup_format_time($signup['opens_at'], 'l j F Y \a\t H:i'));
  } else if ($state === 'closed') {
    $details[] = 'Closed';
  } else {
    $details[] = '<a href="' . htmlspecialchars(signup_member_url($signup['url'], $member)) . '" target="_blank">Sign up</a>';
    if ($signup['closes_at'] !== null) {
      $details[] = 'Closes ' . htmlspecialchars(signup_format_time($signup['closes_at'], 'l j F Y \a\t H:i'));
    }
  }
?>
      <p><?php echo implode(' | ', $details); ?></p>
    </li>
<?php
}
?>
<article>
  <h2>Sign-ups</h2>
  <p>Most of the programme you can just turn up to the room to watch, but some items have limited space and will require signing up in advance.</p>
  <?php
  $signups = current_user_has_permission('see-signup-links') ? signups_sort(db_get_signups()) : [];
  if ($signups) {
    [$category_anchors, $signup_anchors] = signup_anchors($signups);
    $member = is_logged_in() ? [
      'name' => get_current_user_name(),
      'email' => get_current_user_email(),
      'badge_no' => get_current_user_badge_no(),
    ] : null;
    $now = time();
  ?>
  <ul>
<?php
    foreach ($category_anchors as $category => $anchor) {
?>
    <li><a href="#<?php echo htmlspecialchars($anchor); ?>"><?php echo htmlspecialchars($category); ?></a></li>
<?php
    }
?>
  </ul>
  <p>You can also sign-up in person at the <a href="/help#in-person">Info desk</a>.</p>
<?php
    $current_category = null;
    $current_start = null;
    $list_open = false;
    foreach ($signups as $signup) {
      $new_category = $signup['category'] !== $current_category;
      $new_start = $signup['session_start'] !== null && ($new_category || $signup['session_start'] !== $current_start);
      if (($new_category || $new_start) && $list_open) {
        echo "  </ul>\n";
        $list_open = false;
      }
      if ($new_category) {
        $current_category = $signup['category'];
        $current_start = null;
?>
  <h3 id="<?php echo htmlspecialchars($category_anchors[$current_category]); ?>"><?php echo htmlspecialchars($current_category); ?></h3>
<?php
      }
      if ($new_start) {
        $current_start = $signup['session_start'];
?>
  <h4><?php echo htmlspecialchars(signup_format_time($current_start, 'l j F, H:i')); ?></h4>
<?php
      }
      if (!$list_open) {
        echo "  <ul>\n";
        $list_open = true;
      }
      render_signup($signup, $signup_anchors[$signup['id']], $member, $now);
    }
?>
  </ul>
  <?php
  } else {
  ?>
  <p>We will be opening sign-ups closer to the convention. They will be done via an online form. If you need it, there will be a computer at the <a href="/help#in-person">Info desk</a> to sign-up in person.</p>
  <?php
  }
  ?>
  <p>Registrations for a particular day will close at 17:00 on the previous day. If there are more sign-ups for a particular item than there are spaces, successful applicants will be chosen by a random lottery. Results of the lottery will be announced by 19:30 by e-mail (a few hours later on Thursday).</p>
  <p>There is no advantage to signing up for an item early. As long as you fill in the form before sign-ups close at 17:00, you will have an equal chance of getting a spot. A lottery will also be used to select waitlist spots — if you have been waitlisted, you will be able to turn up at an item, and we will let you know if any spaces have become available due to cancellations or no-shows.</p>
</article>
<?php
render_footer();
?>
