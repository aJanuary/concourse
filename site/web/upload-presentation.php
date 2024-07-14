<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Upload presentation',
  'Upload a presentation for a programme item at Glasgow 2024',
  ['Home' => '/', 'Upload presentation']
);
?>
<article>
  <h2>Upload presentation</h2>
  <p>If you have any items that need to be projected or played during your session(s), for example a PowerPoint presentation, please upload them.</p>
  <ul>
    <li><span class="dt">Filename</span> &ndash; The filename should start with the session time in the 24 hour clock, day, and room. For example, "1500 Saturday Hall1 An example presentation name.pptx".</li>
    <li><span class="dt">Format</span> &ndash; A powerpoint file (PPT or PPTX).</li>
    <li><span class="dt">Aspect ratio</span> &ndash; Widescreen (16:9) ratio.</li>
    <li><span class="dt">No external embeds</span> &ndash; Please make sure that any specialist fonts, images, or other media are inserted into the presentation, and not linked to other locations.</li>
  </ul>
  <p><a class="button" href="https://driveuploader.com/upload/gqMV9rFGKB/" target="_blank">Upload your presentation</a></p>
  <p>The deadline for these uploads is the 1<sup>st</sup> of August 2024.</p>
  <p>This deadline, and the following requests, give us time to check that they are usable so that we can have confidence that things will work well during the actual session. Only the AV Tech team will have access to these items.</p>
  <p>We ask that all items to be shown are submitted in this method. This means that the presentation will be ready to go at the start of the item. Other options, such as using your own laptop can lead to significantly increased set up time, and we would appreciate notification ahead of time by e-mailing <a href="mailto:tech@glasgow2024.org">tech@glasgow2024.org</a></p>
  <p>If any of the above is difficult for you, please get in touch with us by e-mailing <a href="mailto:tech@glasgow2024.org">tech@glasgow2024.org</a> and we will confirm whether or not we can accommodate your request.</p>
</article>
<?php
render_footer();
?>