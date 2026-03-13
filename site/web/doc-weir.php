<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-doc-weir');

render_header(
  'Doc Weir',
  'Vote for the Doc Weir Award at ' . CON_NAME . '.',
  ['Home' => '/', 'Doc Weir']
);
?>
<article>
  <h2>Doc Weir Award</h2>
  <p><a href="https://forms.gle/L6eqeqUcmUpVoZVy9" target="_blank" class="button">Vote</a></p>
  <p>Named in memory of Dr Arthur Rose &lsquo;Doc&rsquo; Weir and first presented in 1963, the Award is presented annually to someone who the members of that year's Eastercon think has made a noteworthy contribution to UK fandom. I was absolutely stunned &mdash; and more deeply honoured than I can ever express &mdash; to be chosen as the 2025 winner.</p>
  <p>In common with many others, I do most of my volunteering behind the scenes, creating the programme for our own Satellite Conventions and, in 2024, the Glasgow Worldcon Science Track. This involves hundreds of hours&rsquo; effort over many months &mdash; generating programme ideas, soliciting participants, and permutating programme items &mdash; and then it&rsquo;s all over in just a few days. Receiving the Doc Weir Award is a recognition from fellow fans that your contribution to the community is noticed and appreciated, and that&rsquo;s why it means so much.</p>
  <p>So, please, if you know someone who devotes their time, effort, ideas or support to make things happen in fandom, consider nominating them for the 2026 Doc Weir Award. The Awardee is chosen by means of a write-in ballot, and all members of Iridescence are entitled to vote. You can do it on paper or <a href="https://forms.gle/L6eqeqUcmUpVoZVy9" target="_blank">vote online</a>.</p>
  <p>&mdash; Christine Davidson (2025 Doc Weir Award Winner)</p>
  <img src="/resources/docweir.jpg" alt="Christine Davidson holding the Doc Weir Award">
</article>
<?php
render_footer();
?>
