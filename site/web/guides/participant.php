<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-participant-guides');

render_header(
  'Participant guide',
  'Participant guide for Glasgow 2024',
  ['Home' => '/', 'Guides' => '/guides/list', 'Participant guide']
);
?>
<article class="instructions">
  <h2>Participant guide</h2>
  <p>This is a guide for all programme participants.</p>
  <p>It is complemented by the <a href="/guides/moderator">Panel Moderator Guide</a> and the <a href="/guides/online">Online Programme Participant Guide</a>.</p> 
  <h3>Key Locations and Teams</h3>
  <h4>Programme Operations</h4>
  <p>Located in <a href="/resources/maps/sec2.pdf">Boisdale (in the SEC, behind the Lomond Auditorium)</a>, programme
operations is the main place to go to resolve programme related issues and/or to
have your questions answered. This is where you pick up workshop supplies, and
go for tech help before a session. Feel free to stop in and say hi once the initial rush
of the start of the convention has passed. We also have extra masks in case you
need one for any reason!</p>
<p>Whether you are in person or online, you may also contact the Programme
Operations team by emailing programme@glasgow2024.org. During Programme
Operations opening hours, you can also make queries in the the #programme-ops
channel on <a href="/chat">Discord</a> and we will get back to you in due
course.</p>
  <h5>Opening Hours</h5>
  <ul>
    <li>Wednesday 7th: 14:00 - 18:30</li>
    <li>Thursday 8th, Friday 9th, Saturday 10th, Sunday 11th: 09:00 - 18:00,/li>
    <li>Monday 12th: 09:00 - 15:00</li>
  </ul>
  <h4>Green Room</h4>
  <p>The Programme Green Room for Glasgow 2024 is actually two different Green
Rooms, depending on where in the convention site your programme item is taking
place.</p>
  <p>If your programme item is taking place in the SEC the Green Room is in <a href="/resources/maps/sec2.pdf">Boisdale</a>
(the same place as Programme Operations!), if it is in the Crowne Plaza this is the
<a href="/resources/maps/crowne-plaza.pdf">Mezzanine</a> which is above the hotel reception and bar.</p>
  <p>The Crowne Plaza Green room is open each day until 18:00. If your item is in the
Crowne Plaza and starts <em>after</em> 18:00 you will need to go to the SEC Green Room.</p>
  <p>If you are an online participant on an item with on-site participants, you will be able to
dial in to a Zoom call with them while they are in the Green Room. If you&apos;re taking
part in an online-only panel, your Green Room can be accessed via the Join link for
your panel in Planorama. Refer to the Online Programme Participant Guidelines for
more information.</p>
  <p>On <a href="/chat">Discord</a>, we also have a #green-room channel where you can talk to Green
Room staff, and a #green-room-forum channel where you can start a new post to
talk to your panellists.</p>
  <h5>Green Room SEC Opening Hours</h5>
  <ul>
    <li>Thursday 8th: 11:00 - 21:00</li>
    <li>Friday 9th, Saturday 10th, Sunday 11th: 09:30 - 21:00</li>
    <li>Monday 12th: 09:30 - 15:45</li>
  </ul>
  <h5>Green Room Crowne Plaza Opening Hours</h5>
  <ul>
    <li>Thursday 8th: 12:30 - 18:00</li>
    <li>Friday 9th, Saturday 10th, Sunday 11th: 09:30 - 18:00</li>
    <li>Monday 12th: 09:30 - 14:45</li>
  </ul>
  
  <h3>General Programme Item Guidelines</h3>
  <ul>
    <li>In line with the <a href="https://glasgow2024.org/about/policies/disease-mitigation-policy/">convention&apos;s disease mitigation policy</a>, we
    strongly recommend that you wear a mask in the programme rooms and other
    crowded areas. Programme Ops has masks with a clear plastic in front
    available for programme participants.</li>
    <li>Do not invite anyone onto programme items without confirming that the
    addition or substitution is reviewed and approved by Programme Ops.</li>
    <li>Please be seated on time so that the session can start as soon as most of the
    hustle and bustle of people entering the room has settled down.</li>
    <li>Before identifying yourself, check that you can be heard, while using the
    microphone. Ensure the microphones and tech are working and at proper
    sound / brightness levels. Please make your introduction brief and consider
    including your pronouns, so that the panel can progress quickly.</li>
    <li>Panels, presentations, meetups and table talks: each item lasts for 60
    minutes. Other types of items may run for different lengths. Programme Ops
    will provide a 10-minute warning before the end of the item. Although for
    panels, the moderator is in charge of bringing the item to a close, please be
    considerate of the time so that the moderator does not have to cut you off.</li>
  </ul>
  
  <h3>Guidelines for Different Types of Programme Item</h3>
  <h4>Panels</h4>
  <ul>
    <li>If you are a participant for an item on site, please meet in the appropriate
Green Room 30-40 minutes before panels. This allows you to greet the rest of
the panellists, pick up a drink, and make sure you are on the same page
about the panel. Please note that alcoholic drinks from the SEC Green Room
cannot be taken out of that room.</li>
    <li>You will want to head out to your panel as a group 10-15 minutes before its
start time, to ensure you make it to your programme room in time.</li>
    <li>If you are on an online panel, or are an online participant on a hybrid panel,
please follow the instructions in the Online Programme Participant Guidelines.</li>
  </ul>
  <h4>Presentations</h4>
  <ul>
    <li>If you are presenting on site, ensure you have already sent your slides to the
Tech team or have otherwise agreed how you will present. You can contact
them on <a href="mailto:tech@glasgow2024.org">tech@glasgow2024.org</a>.</li>
    <li>You can pick up your drink from the Green Room 30-40 minutes before your
presentation. Please note that alcoholic drinks from the SEC Green Room
cannot be taken out of that room.</li>
    <li>Please make sure you head to your presentation no later than 10-15 minutes
before its start time, to ensure you make it to your programme room in time,
and also have enough time to check in with the tech staff if needed.</li>
    <li>If you are presenting online, please join Streamyard from the link in
<a href="https://planorama.glasgow2024.org">Planorama</a> at least 20 minutes before your start time to check in and make
sure that your presentation will run.</li>
</ul>
  <h4>Readings</h4>
  <ul>
    <li>
      If you have an on-site reading, please go directly to the room at least 5
    minutes before the start of your reading. You do not need to attend the Green
    Room beforehand.<br>
    Note that if you are scheduled in the second half of an hour&apos;s slot, someone
else will be finishing up their reading at the time.
    </li>
    <li>On site reading slots are 30 minutes long. Please start wrapping up after 25
minutes at most so that the next reader can start on time.</li>
    <li>If you have an online reading, please join Streamyard from the link in
Planorama at least 10 minutes before your start time to check in.
Online reading slots are longer than in-person, but you do not have to use the
whole hour!</li>
  </ul>
  <h4>Table Talks</h4>
  <ul>
    <li>If you are hosting an in-person table talk, please go to the Table Talks Area in
Hall 4 about 10-15 minutes before your start time to check in. If you are
hosting an online table talk, follow the link on your Planorama schedule 10-15
minutes before your start time to check in.</li>
  <li>Some good ways to get the conversation going include: asking your guests
whether they have enjoyed the convention so far, where they have come
from, why they signed up to meet you and so on. You might want to prepare
one or two anecdotes about your work or the topics you listed in the
description in advance.</li>
  <li>Table talks are using online signups and a lottery system to assign
participants for hosts who get more signups than spots. If you want to let
people know more about the process for signing up for Table Talks, you can
direct them to <a href="https://glasgow2024.org/whats-on/programme/tabletalks/">https://glasgow2024.org/whats-on/programme/tabletalks/</a>.</li>
  <li>If there is someone you are concerned about attending your table talk and
want to make sure that doesn&apos;t happen, please come talk to us in Programme
Operations in <a href="/resources/maps/sec2.pdf">Boisdale</a> or email <a href="mailto:programme@glasgow2024.org">programme@glasgow2024.org</a>.</li>
</ul>
  <h4>Workshops</h4>
  <ul>
    <li>If you have a presentation as part of your workshop, ensure you have already
sent your slides to the Tech team or have otherwise agreed how you will
present. You can contact them on <a href="tech@glasgow2024.org">tech@glasgow2024.org</a>.</li>
    <li>You can pick up your drink from the Green Room 30-40 minutes before your
workshop. Please note that alcoholic drinks from the SEC Green Room
cannot be taken out of that room.</li>
    <li>Please make sure you head to your workshop no later than 10-15 minutes
before its start time, to ensure you make it to your programme room in time,
and also have enough time to check in with the tech staff if needed.</li>
    <li>If your workshop is online, please join Streamyard from the link in Planorama
at least 20 minutes before your start time to check in and make sure that any
presentation will run.</li>
    </ul>
  <h4>Meetups</h4>
  <p>Meetups (sometimes known as affinity groups, Birds of a Feather, or fan chats) are
small discussion groups of interest to specific groups of people. They might be
guided discussions or free-form chats, depending on the topic. Smaller meetups of
fan groups and crafters will be in Hall 4 in the designated areas. Other meetups for
affinity and professional groups will be scheduled in specific rooms, and some
meetups will take place online. No sign-up is required but if there are more people
attending than the designated area or room has space for, we ask that you split the
party and find your own space to continue the conversation; for example, in the Hall
4 social space or on the mezzanine in the Crowne Plaza.</p>
  <p>If you are the host of a meetup, please arrive at least five minutes before the start.
You do not need to attend the Green Room beforehand.</p>
  <p>Your role is to facilitate conversation and be the point of contact for any issues. Large
groups are more likely to self-organise into small groups for conversation, but for a
small group, you might need to start the conversations going - it&apos;s a good idea to
prepare a couple of ice-breaker questions around the topic of your meetup, if
appropriate.</p>
  <p>If there are a lot of people in your meetup, we suggest splitting up into smaller
groups for introductions rather than going around the whole group one-by-one
because this can eat up a lot of the allotted time.</p>
  <h4>Stroll With the Stars</h4>
  <p>If you are on a Stroll with the Stars, please meet that day&apos;s group by 09:00 outside
the Crowne Plaza&apos;s main entrance. Each day&apos;s route is wheelchair-accessible, and
the pace is adjusted to match the abilities of the slowest walkers.</p>
  <h4>Book Launches</h4>
  <ul>
    <li>Please show up to your book launch at least 10 minutes in advance. You do
not need to go to the Green Room.</li>
    <li>For group book launches, make yourself known to the host (listed as (m) in
Planorama), who will let you know the order that people will speak, and will
perform introductions.</li>
</ul>
</article>
<?php
render_footer();
?>