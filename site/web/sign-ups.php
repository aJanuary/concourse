<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Sign-ups',
  'Sign-up for programme items at Glasgow 2024',
  ['Home' => '/', 'Sign-ups']
);
?>
<article>
  <h2>Sign-ups</h2>
  <p>Most of the programme you can just turn up to the room to watch, but some items have limited space and will require signing up in advance.</p>
  <p>We will be opening sign-ups on 6<sup>th</sup> August 2024. They will be done via an online form. If you need it, there will be a computer at the <a href="/help">Info desk</a> to sign-up in person.</p>
  <p>Registrations for a particular day will close at 17:00 on the previous day. If there are more sign-ups for a particular item than there are spaces, successful applicants will be chosen by a random lottery. Results of the lottery will be announced by 19:30 by e-mail.</p>
  <p>There is no advantage to signing up for an item early. As long as you fill in the form before sign-ups close at 17:00, you will have an equal chance of getting a spot. A lottery will also be used to select waitlist spots — if you have been waitlisted, you will be able to turn up at an item, and we will let you know if any spaces have become available due to cancellations or no-shows.</p>
</article>
<?php
render_footer();
?>