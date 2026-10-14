<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-time');

render_header(
  'Convention time',
  'Convert times to and from ' . CON_NAME . ' time.',
  ['Home' => '/', 'Convention time']
);
?>
<article>
  <h2>Convention time</h2>
  <?php
    $offset_str = (new DateTime('now', new DateTimeZone(TIMEZONE)))->format('P');
  ?>
  <p>The convention is in the <?php echo htmlspecialchars(TIMEZONE); ?> (UTC<?php echo $offset_str; ?>) timezone.</p>

  <div id="timezone-converter">
    <div class="converter-column">
      <label for="convention-time">Convention time (<?php echo htmlspecialchars(TIMEZONE); ?>)</label>
      <input type="datetime-local" id="convention-time">
      <a href="#" id="current-time-link">Current time</a>
    </div>
    <div class="converter-column">
      <label for="local-timezone">Your timezone</label>
      <input type="datetime-local" id="local-time">
      <select id="local-timezone"></select>
    </div>
  </div>
</article>

<script>
  const CONVENTION_TIMEZONE = <?php echo json_encode(TIMEZONE); ?>;

  const timezones = Intl.supportedValuesOf('timeZone');
  const $timezoneSelect = document.getElementById('local-timezone');
  const $conventionTime = document.getElementById('convention-time');
  const $localTime = document.getElementById('local-time');

  const browserTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  for (const tz of timezones) {
    const option = document.createElement('option');
    option.value = tz;
    option.textContent = tz.replace(/_/g, ' ');
    if (tz === browserTimezone) {
      option.selected = true;
    }
    $timezoneSelect.appendChild(option);
  }

  function formatForInput(date, timezone) {
    const options = {
      timeZone: timezone,
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    };
    // en-CA uses YYYY-MM-DD format
    const parts = new Intl.DateTimeFormat('en-CA', options).formatToParts(date);
    const get = (type) => parts.find(p => p.type === type)?.value || '';
    return `${get('year')}-${get('month')}-${get('day')}T${get('hour')}:${get('minute')}`;
  }

  function parseAsTimezone(dateString, timezone) {
    // Interpret dateString as UTC to get a starting point.
    const utcGuess = new Date(dateString + 'Z');

    // See what time that UTC instant would be in the target timezone.
    const inTz = formatForInput(utcGuess, timezone);

    // The difference tells us the offset to apply.
    const wanted = new Date(dateString + 'Z');
    const got = new Date(inTz + 'Z');
    const offset = wanted - got;

    return new Date(utcGuess.getTime() + offset);
  }

  const now = new Date();
  $conventionTime.value = formatForInput(now, CONVENTION_TIMEZONE);

  function updateLocalTime() {
    const conventionTimeStr = $conventionTime.value;
    if (!conventionTimeStr) {
      return;
    }
    const date = parseAsTimezone(conventionTimeStr, CONVENTION_TIMEZONE);
    const localTimezone = $timezoneSelect.value;
    $localTime.value = formatForInput(date, localTimezone);
  }

  function updateConventionTime() {
    const localTimeStr = $localTime.value;
    if (!localTimeStr) {
      return;
    }
    const localTimezone = $timezoneSelect.value;
    const date = parseAsTimezone(localTimeStr, localTimezone);
    $conventionTime.value = formatForInput(date, CONVENTION_TIMEZONE);
  }

  $conventionTime.addEventListener('input', updateLocalTime);
  $localTime.addEventListener('input', updateConventionTime);
  $timezoneSelect.addEventListener('change', updateLocalTime);

  document.getElementById('current-time-link').addEventListener('click', function(e) {
    e.preventDefault();
    $conventionTime.value = formatForInput(new Date(), CONVENTION_TIMEZONE);
    updateLocalTime();
  });

  updateLocalTime();
</script>

<?php
render_footer();
?>
