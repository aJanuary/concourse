<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/site_selection.php');
require_once(getenv('CONFIG_LIB_DIR') . '/square.php');
require_once(getenv('CONFIG_LIB_DIR') . '/countries.php');

check_permission('see-site-selection');

$badge_no = get_current_user_badge_no();

if (db_get_member_site_selection($badge_no) !== null) {
  header('Location: /site-selection');
  exit;
}

$error = null;
$info = db_get_site_selection_info($badge_no);
$country = $info['country'] ?? '';
$address = $info['address'] ?? '';
$wants_paper_publications = $info['wants_paper_publications'] ?? false;
$consent_to_publish_name = $info['consent_to_publish_name'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $country = (string) ($_POST['country'] ?? '');
  $address = trim((string) ($_POST['address'] ?? ''));
  $wants_paper_publications = isset($_POST['wants_paper_publications']);
  $consent_to_publish_name = isset($_POST['consent_to_publish_name']);

  if ($address === '') {
    $error = 'Enter your postal address.';
  } else if (!array_key_exists($country, COUNTRIES)) {
    $error = 'Choose your country.';
  } else {
    db_set_site_selection_info($badge_no, $country, $address, $wants_paper_publications, $consent_to_publish_name);
    try {
      $payment_link = square_get_or_create_payment_link($badge_no, get_current_user_email());
      header('Location: ' . $payment_link['url']);
      exit;
    } catch (Exception $e) {
      $err_code = log_exception($e);
      $error = "We couldn't start your payment. Please try again, and if it still doesn't work, contact " . SUPPORT_EMAIL . " quoting error $err_code.";
    }
  }
}

$countries = COUNTRIES;
asort($countries, SORT_STRING | SORT_FLAG_CASE);

render_header(
  'Site selection details',
  'Your details for site selection.',
  ['Home' => '/', 'Site selection' => '/site-selection', 'Details']
);
?>
<article>
  <h2>Postal address</h2>
  <p>We use your postal address to check that each member votes only once. We won't send you any post.</p>
<?php
if ($error !== null) {
?>
  <div class="error" id="details-error" role="alert">
    <img src="/resources/error.svg" alt="error" aria-label="error">
    <?php echo htmlspecialchars($error); ?>
  </div>
<?php
}
?>
  <form method="POST" class="site-selection-details">
    <?php echo csrf_field(); ?>
    <p>
      <label for="address">Address, as you'd write it on an envelope <span class="required" aria-hidden="true">*</span></label>
      <textarea id="address" name="address" required<?php if ($error !== null) { ?> aria-describedby="details-error"<?php } ?>><?php echo htmlspecialchars($address); ?></textarea>
    </p>
    <p>
      <label for="country">Country <span class="required" aria-hidden="true">*</span></label>
      <select id="country" name="country" required>
        <option value="">Choose a country</option>
<?php
foreach ($countries as $code => $name) {
?>
        <option value="<?php echo htmlspecialchars($code); ?>"<?php echo $country === $code ? ' selected' : ''; ?>><?php echo htmlspecialchars($name); ?></option>
<?php
}
?>
      </select>
    </p>
    <h2>Preferences</h2>
    <p class="checkbox">
      <label>
        <input type="checkbox" name="consent_to_publish_name" value="1"<?php echo $consent_to_publish_name ? ' checked' : ''; ?>>
        The winning bid may list my name as a member on their website and in their progress reports.
      </label>
    </p>
    <p class="checkbox">
      <label>
        <input type="checkbox" name="wants_paper_publications" value="1"<?php echo $wants_paper_publications ? ' checked' : ''; ?>>
        I would like paper publications from the winning bid. They may charge extra for this.
      </label>
    </p>
    <p><input type="submit" value="Continue to payment"></p>
    <p>Payments are taken by Square. We don't see or keep your card details. See Square's <a href="https://squareup.com/legal/privacy" target="_blank">privacy notice</a>.</p>
  </form>
</article>
<?php
render_footer();
?>
