<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

function render_signup_link($signup) {
  $name = $signup['name'];
  $guide_url = $signup['guide_url'];
  $url = $signup['url'];
  $open_date = $signup['open_date'];
  $close_date = $signup['close_date'];

  $id = trim(strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($name))), '-');
  $heading = '<h4><a href="#' . $id . '" name="' . $id . '" style="color: #333; text-decoration: none;">' . htmlspecialchars($name) . '</a></h4>';

  if ($signup['notes'] ?? null) {
    $heading .= '<p><em>' . htmlspecialchars($signup['notes']) . '</em></p>';
  }

  if ($open_date !== null && date('Y-m-d H:i') < $open_date) {
    $formatted = date('j F Y \a\t H:i', strtotime($open_date));
    $link = 'Opens ' . htmlspecialchars($formatted);
  } else {
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
    $link = '<a href="' . htmlspecialchars($url) . '" target="_blank">Sign up</a>';
  }

  $details = '<a href="' . htmlspecialchars($guide_url) . '" target="_blank">More information</a> | ' . $link;

  if ($close_date !== null) {
    $formatted_close = date('j F Y \a\t H:i', strtotime($close_date));
    $details .= ' | Closes ' . htmlspecialchars($formatted_close);
  }

  return $heading . '<p>' . $details . '</p>';
}

render_header(
  'Sign-ups',
  'Sign-up for programme items at ' . CON_NAME . '.',
  ['Home' => '/', 'Sign-ups']
);
?>
<article>
  <h2>Sign-ups</h2>
  <p>Most of the programme you can just turn up to the room to watch, but some items have limited space and will require signing up in advance.</p>
  <p>If there are more sign-ups for a particular item than there are spaces, successful applicants will be chosen by a random lottery. Results of the lottery will be announced by e-mail. Results for workshops and table wilks will also be posted at the registration desk.</p>
  <p>There is no advantage to signing up for an item early. As long as you fill in the form before sign-ups close, you will have an equal chance of getting a spot.</p>

<?php
  $signups = [
    [
      'name' => 'Beating Procrastination [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1155',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLScOyq5fY_BNaSvVO6CF7LYqTClV1APdu272l7bMwI8tiGRvXQ/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-05 09:30',
    ],
    [
      'name' => 'Begin to Knit [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1015',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLScRR6O4l5PaW8843t-fr9R9viRIuNsLfokpvCpyHcLVHemShA/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-03 19:00',
    ],
    [
      'name' => 'Build a World [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1126',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSdaAi2FIZisf3nAVJk2yi8p8cZAQjkIi8fleXJ8Mj-dKRVYMw/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-04 09:30',
    ],
    [
      'name' => 'Demystifying Publishing Contracts [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1130',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSc61lcNq-QgbEeG2tcuAiYk_boUUp2SJt1QPutZhD0VwXhgAQ/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-01 18:00',
      'close_date' => '2026-04-03 09:30',
    ],
    [
      'name' => 'Try Not to Stab Yourself - Introduction to Embroidery [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1133',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLScxpzOZjMILRh8qPbkWTdFthtqCPQPvClZ7CwcZl5TTAB55rw/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-05 19:00',
    ],
    [
      'name' => 'Table Talk with Emma Newman [TABLE TALK]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1172',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSfebg67wbHzS0VOZKYC3kspE0M6sbF7UbyDT7dGjtfGwDJPJQ/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-04 19:00',
    ],
    [
      'name' => 'Erasure Poetry Workshop [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1210',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSdcnbXgFXmNBifCALOBB1uRBN4PNCqfJ8GUsPkIojAU1Zy0EQ/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-04 09:30',
    ],
    [
      'name' => 'How to Crochet a Dragon [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1132',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSda_hU7qDNyqatqgnyt9T0zI0snUzXV0mPau7j5rqwqfSZOTQ/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-04 19:00',
    ],
    [
      'name' => 'How to get a Literary Agent [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1138',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSf1c9Dyt2b9u7gi10J0jaFaBHXSKLoMLwpvBca9DwUcanrlDg/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-04 09:30',
    ],
    [
      'name' => 'Microfictions [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1210',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSfM2UQQStw2fDVn45ohSW8IlgPmFeH74XQYoPugqHqPsEgnUw/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-04 09:30',
    ],
    [
      'name' => 'Neurodivergent Strategies for Overcoming Writer\'s Block [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1128',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSfGw5oNDyt5oGY4KUrhxeOSe5j09zwYy6-Lw3MDgDkrYmwx5Q/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-04 09:30',
    ],
    [
      'name' => 'Poetry Open Mic [OPEN MIC]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1102',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSeb1C2jtx5Qpo8JtmSRwB8DkGbZkPv3UhUiYXLhbSOOm0s4Lg/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-01 18:00',
      'close_date' => '2026-04-03 20:00',
      'notes' => 'We won\'t be emailing people about whether they have a slot in the open mic or not. The hosts will pick people to go up on the night.',
    ],
    [
      'name' => 'Table Talk with RJ Barker [TABLE TALK]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1171',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSfLZsvAJakUVi95IeU_waelwaHv3OJ5TB4l_ftQAmkGMJawOw/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-05 09:30',
    ],
    [
      'name' => 'Self Publishing 101 [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1190',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSdSBimn--j1_ojapxGaWGj68dByt0-NiBy-wZdilv3qkXhlbQ/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-05 19:00',
    ],
    [
      'name' => 'Spinning with a Drop Spindle [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1131',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLScVKqL7Q4bBwDg7zFgBGp7qljr-jUnEpEB4pN_4sKsGquF8Pw/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-04 19:00',
    ],
    [
      'name' => 'Writing Superheroes [WORKSHOP]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1210',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSfTsAUjZRcahCGoYdhhU_3oFvbwZp6bsOmqXXWxpEvSYoTYpA/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-03 14:00',
      'close_date' => '2026-04-04 09:30',
    ],
    [
      'name' => 'Belly of the Beast (Mothership) [GAME]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1154',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSeaKIRWDKYBv7dR_hPCVtJw6VCNqxAUXqCnLslFAQ_2lKldhg/viewform?usp=pp_url&entry.964998266=NAME&entry.1161268536=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-02 14:00',
      'close_date' => '2026-04-04 19:00',
    ],
    [
      'name' => 'Get Me the Anderson Contract (MORK ORG) [GAME]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1149',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSdt41Et0kQVz7Id7bmZh9RXhnjDIwiPZHJ-KCl-QhTRAwKe_Q/viewform?usp=pp_url&entry.964998266=NAME&entry.75376202=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-02 14:00',
      'close_date' => '2026-04-03 19:00',
    ],
    [
      'name' => 'Shipwrecked on Shank Island (PIRATE BORG) [GAME]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1150',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSdAH-uMP0jlk6Vdtp80ji2rzozdis43ao9bGmyF0SXwDMj4VA/viewform?usp=pp_url&entry.964998266=NAME&entry.1173501367=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-02 14:00',
      'close_date' => '2026-04-04 13:30',
    ],
    [
      'name' => 'Starfleet Academy 2270: Milk Run (The Code of the Spacelanes) [GAME]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1206',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLScaJuTKtNZw7G_7ax8au6H6BH9KywZNpwbUgNnIniCcMj4R3A/viewform?usp=pp_url&entry.964998266=NAME&entry.294137761=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-02 14:0000',
      'close_date' => '2026-04-05 19:00',
    ],
    [
      'name' => 'The Curse of the Witch Queen (Dungeons & Dragons 5e) [GAME]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1146',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSfKhjdTIHdZe7Llgod7yoSSyWFCmXdo4IwOaFTSkvoKp5nBgQ/viewform?usp=pp_url&entry.964998266=NAME&entry.929293668=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-02 14:00',
      'close_date' => '2026-04-04 10:30',
    ],
    [
      'name' => 'The Legend of the Condor (The Elephant and Macaw Banner) [GAME]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1192',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSd-AXfNshtTJdePRQmfZgOuVeMACkG35QSu3O1NqbO1vMwJ0g/viewform?usp=pp_url&entry.964998266=NAME&entry.58587696=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-02 14:00',
      'close_date' => '2026-04-05 13:30',
    ],
    [
      'name' => 'The Maze of Mad Malkas (Dungeons & Dragons 5e) [GAME]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1147',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLScxpREDQ5jLrG8BaXnyMq2puiSQVkt03G5tI3-Hey6uDE8Ncw/viewform?usp=pp_url&entry.964998266=NAME&entry.310051586=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-02 14:00',
      'close_date' => '2026-04-05 10:30',
    ],
    [
      'name' => 'The Tattoo Job (Blades in the Dark) [GAME]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1152',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLScUTqfzDnvQwBw7JGn_z2E9TrMD-fDNeqfn-vjj6rrp8RTCig/viewform?usp=pp_url&entry.964998266=NAME&entry.1978841918=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-02 14:00',
      'close_date' => '2026-04-03 13:30',
    ],
    [
      'name' => 'When the Bough Breaks (Wildsea) [GAME]',
      'guide_url' => 'https://guide.eastercon2026.org/id/1153',
      'url' => 'https://docs.google.com/forms/d/e/1FAIpQLSfgarqItwArmAEpOHMI6owDV8SQWfFm5NWPZGjc684w5Xwzbg/viewform?usp=pp_url&entry.964998266=NAME&entry.314637674=EMAIL&entry.1782361168=MEMBER+NO',
      'open_date' => '2026-04-02 14:00',
      'close_date' => '2026-04-03 13:30',
    ],
  ];

  $not_yet_open = [];
  $open = [];
  $closed = [];

  $now = new DateTime();

  foreach ($signups as $signup) {
    $open_date = $signup['open_date'] !== null ? new DateTime($signup['open_date']) : null;
    $close_date = $signup['close_date'] !== null ? new DateTime($signup['close_date']) : null;

    if ($open_date !== null && $now < $open_date) {
      $not_yet_open[] = $signup;
    } elseif ($close_date !== null && $now > $close_date) {
      $closed[] = $signup;
    } else {
      $open[] = $signup;
    }
  }

  usort($open, function ($a, $b) {
    $close_a = new DateTime($a['close_date']);
    $close_b = new DateTime($b['close_date']);
    if ($close_a != $close_b) {
      return $close_a <=> $close_b;
    }

    return strcasecmp($a['name'], $b['name']);
  });

  usort($not_yet_open, function ($a, $b) {
    return strcasecmp($a['name'], $b['name']);
  });

  usort($closed, function ($a, $b) {
    return strcasecmp($a['name'], $b['name']);
  });

  if (!empty($open)) {
    echo '<h3>Open For Sign-ups</h3>';
    foreach ($open as $signup) {
      echo render_signup_link($signup);
    }
  }

  if (!empty($not_yet_open)) {
    echo '<h3>Opening Soon</h3>';
    foreach ($not_yet_open as $signup) {
      echo render_signup_link($signup);
    }
  }

  if (!empty($closed)) {
    echo '<h3>Sign-ups Closed</h3>';
    echo '<p>If the lottery has not yet been drawn for a closed item, you can still try to sign up, but you are not guaranteed to be in the draw.</p>';
    foreach ($closed as $signup) {
      echo render_signup_link($signup);
    }
  }
?>
</article>
</article>
<?php
render_footer();
?>