<?php
require_once('../includes/config.php');
require_once('../includes/session_auth.php');
require_once('../includes/template.php');

if (!current_user_has_permission('see-newsletter')) {
  header('Location: /');
  exit;
}

render_header();
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Newsletter</h3>

<?php
  $files = glob("resources/newsletter/*.pdf");
  // Sort files by pulling out \d+ and sorting numerically
  usort($files, function($a, $b) {
    preg_match('/\d+/', $a, $a_num);
    preg_match('/\d+/', $b, $b_num);
    return $a_num[0] - $b_num[0];
  });
  echo "<ul id=\"newsletter\">";
  foreach ($files as $file) {
    $pdf_filename = basename($file);
    $thumbnail = "resources/newsletter/thumbnails/" . str_replace(".pdf", ".png", $pdf_filename);
    if (!file_exists($thumbnail)) {
      $im = new Imagick($file."[0]");
      $im->setImageFormat('png');
      $im->writeImage($thumbnail);
      $im->clear();
      $im->destroy();
    }
    $basename = basename($pdf_filename, ".pdf");

    $formats = glob("resources/newsletter/" . $basename . ".*");
    usort($formats, function($a, $b) {
      if (substr($a, -3) === "pdf") {
        return -1;
      }
      if (substr($b, -3) === "pdf") {
        return 1;
      }
      return strcmp($a, $b);
    });

?>
    <li>
      <a href="/resources/newsletter/<?php echo $pdf_filename; ?>">
        <img src="/<?php echo $thumbnail; ?>" alt="Newsletter cover">
      </a>
      <p>
        <?php echo $basename; ?>
<?php
    foreach ($formats as $format) {
      $filename = basename($format);
      $ext = pathinfo($format, PATHINFO_EXTENSION);
?>  
        <a href="/resources/newsletter/<?php echo $filename; ?>">(<?php echo $ext; ?>)</a>
<?php
    }
?>
      </p>
    </li>
<?php
  }
?>
</article>

<?php
render_footer();
?>