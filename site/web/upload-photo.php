<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Upload your photo',
  'Upload a photo for ' . CON_NAME . '.',
  ['Home' => '/', 'Upload your photo']
);
?>
<article>
  <h2>Upload your photo</h2>
  <h3>For those not with us in-person</h3>
  <p>In the opening ceremony we will be showing a montage of photos from <?php echo CON_SHORT_NAME; ?> members who aren't with us on-site.</p>
  <p>If you are a member, and aren't with us on-site (either because you bought an online membership, or you bought an in-person membership but can't make it in person) then we invite you to submit a photo for the montage.</p>
  <?php
      if (is_anonymous()) {
  ?>
        <p><a class="button" href="<?php echo make_login_link(); ?>">Log in to upload a photo</a></p>
  <?php
      } else {
  ?>
        <p><a class="button" target="_blank" href="https://example.com/upload-photo?username=<?php echo urlencode($GLOBALS['app-data']['username']); ?>">Upload photo</a></p>
  <?php
      }
  ?>
  <p>The photos, and optionally badge name, will be shown in the opening ceremony, which will be live-streamed and made available for replay to <?php echo CON_SHORT_NAME; ?> members.
</article>
<?php
render_footer();
?>