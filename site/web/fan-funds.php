<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-fan-funds');

render_header(
  'Fan funds',
  'Vote for fan funds at ' . CON_NAME . '.',
  ['Home' => '/', 'Fan funds']
);
?>
<article>
  <h2>Fan funds</h2>
  <p>The fan funds exist to send fans from “here” to “over there” and then for “over there” to send their fans back “here”.</p>

  <h3>TAFF</h3>
  <p>It all started in the 1950s with the Transatlantic Fan Fund (TAFF), which this year will send either Kat Templeton or Lisa Hertel from North America (“over there”) to Metropol Con (in Berlin; “here”).</p>
  <p><a href="https://taff.org.uk/vote.php" target="_blank" class="button">Vote in TAFF</a></p>

  <h3>GUFF</h3>
  <p>The Going Under Fan Fund (GUFF) does the same thing but in that case “over there” is Oceania. That race has finished by the time you read this as Farah Mendlesohn is going  “over there” but keep an eye out in future years!</p>

  <h3>EFF</h3>
  <p>Nowadays, some fan funds send fans from “here” to “here”. The European Fan Fund (EFF) is an example, to send a European fan to Metropol Con.</p>
  <p><a href="https://effund.github.io/" target="_blank" class="button">Vote in EFF</a></p>
</article>
<?php
render_footer();
?>
