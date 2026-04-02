<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-programme-handouts');

function render_handout_section($title, $files, $available_at = null) {
  $tz = new DateTimeZone(TIMEZONE);
  $now = new DateTime('now', $tz);
  if ($available_at !== null && is_string($available_at)) {
    $available_at = new DateTime($available_at, $tz);
  }
?>
<?php $anchor = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-')); ?>
  <h3 id="<?php echo $anchor; ?>"><a href="#<?php echo $anchor; ?>"><?php echo htmlspecialchars($title); ?></a></h3>
  <?php if ($available_at !== null && $now < $available_at) { ?>
    <p>The handouts will be available at <?php echo $available_at->format('H:i \o\n l, j F'); ?>.</p>
  <?php } else { ?>
    <ul>
      <?php
        foreach ($files as $file) {
          if (isset($file['url'])) {
        ?>
            <li><p><a href="<?php echo htmlspecialchars($file['url']); ?>"><?php echo htmlspecialchars($file['label']); ?></a></p></li>
        <?php
          } else {
        ?>
            <li><p><a download href="handouts/<?php echo htmlspecialchars($file['file']); ?>"><?php echo htmlspecialchars($file['label']); ?></a></p></li>
      <?php
          }
        }
      ?>
    </ul>
  <?php } ?>
<?php
}

render_header(
  'Programme handouts',
  'Programme handouts for ' . CON_NAME . '.',
  ['Home' => '/', 'Programme handouts']
);
?>
<article>
  <h2>Programme handouts</h2>

<?php
  render_handout_section('Self Publishing 101 Workshop', [
    [
      'label' => 'HANDOUT SELF PUB WORKSHOP EASTERCONJ 26.docx',
      'file' => 'HANDOUT_SELF_PUB_WORKSHOP_EASTERCONJ_26.docx',
    ],
  ], '2026-04-06 09:00');

  render_handout_section('How to Crochet a Dragon', [
    [
      'label' => 'Google Drive',
      'url' => 'https://drive.google.com/drive/folders/1ItkkCLtMqmlPHlfoujkknIl8F6MCg0yA',
    ],
  ], '2026-04-05 10:30');
?>

</article>
<?php
render_footer();
?>
