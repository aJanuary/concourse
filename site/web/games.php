<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = 'always';
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-games');

function signup_link($url) {
  if (!is_logged_in()) {
    $url = preg_replace('/\?.*$/', '', $url);
  } else {
    $name = urlencode(get_current_user_name());
    $email = urlencode(get_current_user_email());
    $badge_no = urlencode(get_current_user_badge_no());
    $url = str_replace('NAME', $name, $url);
    $url = str_replace('EMAIL', $email, $url);
    $url = str_replace('MEMBER+NO', $badge_no, $url);
  }
  return '<a href="' . htmlspecialchars($url) . '" target="_blank">Sign up</a>';
}

render_header(
  'Games',
  'Games at ' . CON_NAME . '.',
  ['Home' => '/', 'Games']
);
?>
<article>
  <h2>Games</h2>

  <h3>Games room</h3>

  <p>The Games Room will be located in <a href="https://map.eastercon2026.org/room/london">London</a> and contains board games and tables for play. This is also where the RPGs listed on the schedule will be played! You’re welcome to hang out in the Games Room when people aren’t playing, but if people are looking for a space to play a game, please make way for them and move into other areas of the con.
  Overflow gaming space is available in the bar or in <a href="https://map.eastercon2026.org/room/stairs-mezzanine">Ascot and Sunningdale</a>, but note that the latter rooms are only accessible by going up some stairs.</p>
  <p>We are looking for volunteers to look after the Games Room during the convention; if you are interested, please visit <a href="https://map.eastercon2026.org/room/oxford">Ops in Oxford</a> or talk to John Coxon in the Games Room.</p>
  <p>The opening times are as follows:</p>
  <dl class="times-list">
    <div>
      <dt>Friday, 3 April</dt>
      <dd>15:00&ndash;21:00</dd>
    </div>
    <div>
      <dt>Saturday, 4 April</dt>
      <dd>10:00&ndash;21:00</dd>
    </div>
    <div>
      <dt>Sunday, 5 April</dt>
      <dd>10:00&ndash;21:00</dd>
    </div>
    <div>
      <dt>Monday, 6 April</dt>
      <dd>10:00&ndash;14:00</dd>
    </div>
  </dl>

  <h3>Game sign-ups</h3>
  <p>We are running some games which are listed on <a href="https://guide.eastercon2026.org">ConClár</a>. If you are interested in signing up for these, fill in one of the forms below. Content warnings and age ranges for each game are available on the programme or in the sign-up form. Pre-generated characters, dice and pencils will be available for all roleplaying games.</p>
  
  <h4>Belly of the Beast [Mothership]</h4>
  <p>The crew spot an old space hulk orbiting an unknown moon. Its age suggests it might contain riches and powerful loot. It should be simple…</p>
  <p>This is the introductory adventure for Othership by Christopher John Eggett, played using the full Mothership rules.</p>
  <p><?= signup_link('https://docs.google.com/forms/d/e/1FAIpQLSeaKIRWDKYBv7dR_hPCVtJw6VCNqxAUXqCnLslFAQ_2lKldhg/viewform?usp=pp_url&entry.964998266=NAME&entry.1161268536=EMAIL&entry.1782361168=MEMBER+NO') ?></p>

  <h4>Get Me the Anderson Contract [MÖRK ORG]</h4>
  <p>Welcome to the worst office in the world! MÖRK ORG is an adaptation of the super-easy-to-learn MÖRK BORG game. It's set in an evil office filled with micromanagement and murder. Find the Anderson Contract before 5pm... or die trying!</p>
  <p><?= signup_link('https://docs.google.com/forms/d/e/1FAIpQLSdt41Et0kQVz7Id7bmZh9RXhnjDIwiPZHJ-KCl-QhTRAwKe_Q/viewform?usp=pp_url&entry.964998266=NAME&entry.75376202=EMAIL&entry.1782361168=MEMBER+NO') ?></p>

  <h4>Shipwrecked on Shank Island [PIRATE BORG]</h4>
  <p>Cursed scallywags the lot of ye! Join us for an adventure of zombie skeletons, pints of grog and, if you are really lucky, some of sort of treasure! PIRATE BÖRG suits 3–5 mouldy adventurers and a GM.</p>
  <p><?= signup_link('https://docs.google.com/forms/d/e/1FAIpQLSdAH-uMP0jlk6Vdtp80ji2rzozdis43ao9bGmyF0SXwDMj4VA/viewform?usp=pp_url&entry.964998266=NAME&entry.1173501367=EMAIL&entry.1782361168=MEMBER+NO') ?></p>

  <h4>Starfleet Academy 2270: Milk Run [The Code of the Spacelanes]</h4>
  <p>Starfleet cadets take a simple shuttle training mission around the Solar System. It is supposed to be a routine milk run. Naturally, things do not go according to plan… A light, cinematic Star Trek adventure set just after the Original Series. Players begin with familiar characters in a short prologue before creating their own Starfleet cadets and discovering that even training missions can turn into real adventures.</p>
  <p><?= signup_link('https://docs.google.com/forms/d/e/1FAIpQLScaJuTKtNZw7G_7ax8au6H6BH9KywZNpwbUgNnIniCcMj4R3A/viewform?usp=pp_url&entry.964998266=NAME&entry.294137761=EMAIL&entry.1782361168=MEMBER+NO') ?></p>

  <h4>The Curse of the Witch Queen [Dungeons & Dragons 5e]</h4>
  <p>You wake on a small sailing boat with no recollection of how you got there. After fighting off a siren, you land on a long-lost island. Can you break the curse of the witch queen and lift the fog that traps you?</p>
  <p><?= signup_link('https://docs.google.com/forms/d/e/1FAIpQLSfKhjdTIHdZe7Llgod7yoSSyWFCmXdo4IwOaFTSkvoKp5nBgQ/viewform?usp=pp_url&entry.964998266=NAME&entry.929293668=EMAIL&entry.1782361168=MEMBER+NO') ?></p>

  <h4>The Legend of the Condor [The Elephant and Macaw Banner]</h4>
  <p>Welcome to a fantastical 16th century Brazil. A noble has gone missing while searching for the Holy Grail! It’s worth a very handsome reward that could set you up in Brazil if you can find him. The only problem is they are only hiring banners (groups of experts) and you don’t have one – but maybe others have turned up needing a banner too? Find your banner, find the noble and get rich. Gain the favour of the Captain General, appointed by the King of Portugal to run Portugal’s interests, and you could be set for life. This won’t be easy but nothing worthwhile ever is.</p>
  <p><?= signup_link('https://docs.google.com/forms/d/e/1FAIpQLSd-AXfNshtTJdePRQmfZgOuVeMACkG35QSu3O1NqbO1vMwJ0g/viewform?usp=pp_url&entry.964998266=NAME&entry.58587696=EMAIL&entry.1782361168=MEMBER+NO') ?></p>

  <h4>The Maze of Mad Malkas [Dungeons & Dragons 5e]</h4>
  <p>You are a member of an adventuring party looking for your next mission. After being attacked by goblins, you rest in a small town that is oddly quiet. You're approached by the mayor, who begs for your assistance: the minotaur Malkas has stolen all the children! It's up to you to solve his maze and rescue them.</p>
  <p><?= signup_link('https://docs.google.com/forms/d/e/1FAIpQLScxpREDQ5jLrG8BaXnyMq2puiSQVkt03G5tI3-Hey6uDE8Ncw/viewform?usp=pp_url&entry.964998266=NAME&entry.310051586=EMAIL&entry.1782361168=MEMBER+NO') ?></p>

  <h4>The Tattoo Job [Blades in the Dark]</h4>
  <p>Play as a daring scoundrel and carry out a heist in the electroplasmic industrial-fantasy city of Doskvol.</p>
  <p><?= signup_link('https://docs.google.com/forms/d/e/1FAIpQLScUTqfzDnvQwBw7JGn_z2E9TrMD-fDNeqfn-vjj6rrp8RTCig/viewform?usp=pp_url&entry.964998266=NAME&entry.1978841918=EMAIL&entry.1782361168=MEMBER+NO') ?></p>

  <h4>When the Bough Breaks [Wildsea]</h4>
  <p>Board an oversized chainsaw and sail the high trees in Wildsea! While anchored at a safe harbour to wait out an upswelling of mutagenic Crezzarin, you become aware of other newcomers to town who preach of a doom to befall all who stay there.</p>
  <p><?= signup_link('https://docs.google.com/forms/d/e/1FAIpQLSfgarqItwArmAEpOHMI6owDV8SQWfFm5NWPZGjc684w5Xwzbg/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO') ?></p>
</article>

<?php
render_footer();
?>
