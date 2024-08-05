<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-participant-guides');

render_header(
  'Moderator guide',
  'Moderator guide for Glasgow 2024',
  ['Home' => '/', 'Guides' => '/guides/list', 'Moderator guide']
);
?>
<article class="instructions">
  <h2>Moderator guide</h2>
  <h3>Overview</h3>
  <p>Moderators are panellists who are designated to provide structure for and to lead panel
discussions. Moderators are encouraged to participate fully in the conversation if a panel has 4
or fewer total panellists. If a panel has 5 or more people in total, the moderator should
primarily act as a guide for the discussion, but can make some contributions.
Unless otherwise noted, panels at Glasgow 2024 are intended to be 60 minutes long within a
90 minute time slot. Please ensure that your panels finish by the 60 minute mark so that the
room has some time to air out between sessions. (This both supports our disease mitigation
policy and helps to keep crowding in the corridors to a minimum). If audience members want
to talk to panellists after the panel, please direct them to take conversations to uncrowded
areas away from the panel room.</p>

  <h3>Timeline of Moderator Duties</h3>
  <ul>
    <li>Pre-con: Discuss the panel with the panellists via email</li>
    <li>At con, in the Green Room, pre-panel: Meet with the panellists</li>
    <li>At con, in the Panel Room, pre-panel: Pick your seat, facilitate the panellist mic checks</li>
    <li>At con, in the Panel Room, at the start of the panel: Announce the title and topic, state your comment/question policy, facilitate the panellist introductions</li>
    <li>At con, in the panel room, during the panel: Keep the panel on track, make sure panellists get equal opportunities to participate, repeat/rephrase audience questions into the mic, contact Op / Tech / Programme Ops if required.</li>
    <li>At con, in the panel room, at the end of the panel: Wrap up the panel, end the panel on time.</li>
    <li>At con, in the panel room, after the panel: Encourage people to continue any conversations outside of the panel room and on Discord in the thread for the panel.</li>
</ul>

  <h3>Pre-Con</h3>
  <p>Before the convention starts, moderators should have contacted the other participants by
email to address any concerns and to share any resources (useful links, recommended
watching/reading, etc) that are relevant to the panel. This email can outline questions the
moderator might like to open with and ask if the panellists have topics they&apos;d like to discuss or
have questions that they&apos;d like to ask. You can also let them know if you have a particular
moderating style. Setting expectations on all sides is important for the smooth running of
panels. If any of your panellists have not shared their contact information, you can reach out
for assistance at <a href="mailto:programme@glasgow2024.org">programme@glasgow2024.org</a>,
but please note any assistance on this would
be on a best effort basis and we may not be able to help.</p>

  <p>Most panels are intended as discussions, led by the moderator &mdash; if your panel will have a
different format, discuss it with your panellists in advance and make a plan.</p>

<p>Let them know that they should keep their introductions on the panel brief. You should also let
the participants know of any ground rules you may have, for example don&apos;t just promote your
own books, don&apos;t talk over other speakers whether on the panel or in the audience, etc. Please
note, some participants have not given permission to share email addresses, in which case
you should catch them up on any pre-con discussion in the Green Room.</p>

<p>We also have a #green-room-forum channel on <a href="/chat">Discord</a> where you can start a new post to talk
to your panellists.</p>

  <p>If you or your panellists want to show any slides or video during a panel, make sure that all
panellists are happy with this and speak to the Tech team (via email to
<a href="mailto:tech@glasgow2024.org">tech@glasgow2024.org</a>) at least two days before your programme item to see if a presentation
is even possible. Unless your panel requires visuals &mdash; because it&apos;s an art panel, for example &mdash;
you should not put together a presentation. No presentation will be run unless Tech are told
about it in advance.</p>

  <h3>In the Green Room</h3>
  <h4>In-person</h4>
  <p>Please meet in the Green Room (either Boisdale in the SEC or the balcony in the Crowne
Plaza above hotel reception) 30-40 minutes before the panel if you are able. If your panel is
a hybrid one with remote panellists, they will join you via Zoom on the laptop on your Green
Room table.</p>
  <p>Break the ice with the panellists, continue the pre-con discussion (if you were able to have
one), or touch base about what you want to discuss if you can. It is good to quickly review
panel description, purpose and format with the participants even if you have already done this.</p>
  <p>Please check with the participants if there are any topics to avoid.</p>
  <p>Do not add anyone to panels at the con without checking with Programme Ops.</p>
  <p>You should start making your way over to the panel room 10-15 minutes before the panel start
time, depending on its location in the convention centre or hotel.</p>
  <h4>Online</h4>
  <p>If your panel is online-only, you will join Streamyard by following the link on your Planorama
schedule, and you will be able to talk to your panellists as they join.</p>

  <p>If your panel is a hybrid one which you are connecting to remotely, you will join Zoom by
following the link on your Planorama schedule. The Zoom host will then move you to a
breakout room so you can talk to the panellists who are in Glasgow and online.</p>

  <h3>Beginning the Panel</h3>
  <p>Please be aware, there are access spaces for mobies/wheelchairs, and access seating people
who need to sit near the front. However, if these are empty as the panel begins, they may be
used by people without access needs, but be aware if anyone with access needs arrives
during the panel, these spaces should be made available for them. In addition, please ask the
audience to keep the aisles and doors clear for safety reasons.</p>
  <p>Start the panel with an appropriate greeting and the title of the panel, introduce yourself and
give a brief description of the subject. We also recommend letting the audience know if you will
accept questions during the panel or only at the end, to set expectations and minimise
possible interruptions.</p>
  <p>From here, let the participants provide brief introductions, and move the panel along if they
take too long. As discussed in the Green Room, they should keep their introductions brief and
you should be ready to jump in with a quick “thank you” and move immediately to the next
person or begin the panel.</p>
  <p>When everyone sits down, make sure you are in the seat you prefer. You may ask panellists to
move if need be. If you are in a seat where you feel you cannot best fulfil the job of moderator,
the panel will not go as well as it could. If you sit at the end, make sure you aren&apos;t too focused
on those nearest to you. If you are in the middle, make a point to check both sides frequently.
If some panellists have not been saying much, turn to them and ask if there&apos;s anything they&apos;d
like to add. If they say they haven&apos;t, don&apos;t press them, thank them and move on. If you
establish a pattern of going to each panellist in turn early, they tend to continue to follow that
pattern, better ensuring everyone has a chance to speak.</p>

  <h3>Microphone Use</h3>
  <p>Remind panellists to project their voices towards the microphones if they are not doing so -
this is an accessibility issue! Our microphones should be set up for passive pickup, and we
would like participants to avoid touching them unless necessary.</p>
  <p>In some larger rooms, a Tech person will be available to solve any microphone issue. Pay
attention to the Tech person in case they signal that a panellist is inaudible or is too loud. Often
this can be fixed by adjusting distance from the microphone. If a Tech person is not available
and the microphones are not working in your panel room, please report this issue to
Programme Ops, which can be done by texting or calling +44 7899 498828.</p>

  <p>Facing the audience instead of the other participants can be challenging when it is a
conversational atmosphere, but gentle reminders to speak to the audience work well.</p>
  <p>During the Q&A some rooms will NOT have a microphone for audience questions, so you will
need to repeat questions from the audience to ensure everyone hears them. You can rephrase
questions for clarity and brevity, but please try and keep the intent of the question being asked.</p>

  <h3>During the Panel</h3>
  <p>Stay engaged in the conversation and take notes if needed. Make sure all panellists have
equal opportunity to participate in the conversation - this does not mean giving all panellists
exactly equal time (for example, some people may choose to be brief rather than verbose) but
rather ensuring no panellist is overlooked or no single person dominates the conversation. Pay
special attention to making sure that you are maximising the inclusion of diverse voices among
both panellists and audience, such as People of Color or those of other genders on mostly
male panels (or other combinations).</p>
  <p>If you have some pre-prepared questions you want to ask panellists, it is usually best to let
them know these questions in advance. We often find that most panels work best as a free
flowing discussion where participants are able to present their relevant ideas and opinions,
rather than having a rigid structure strictly determined by the moderator. Encourage discussion
between panellists, rather than just having them answer your questions.</p>
  <p>However, you should try to prevent the discussion from drifting away from the topic. Be firm
but fair when redirecting people. If someone goes off-topic into a subject that you know is
covered at another panel, you can plug that panel then steer the discussion back on course.
Another useful tip is to use the phrase “That&apos;s a subject for another panel.” to help get back on
track. While someone is off-topic, think of a question you can use to bring the topic back on
course. Having this ready immediately after you say “that&apos;s a subject for another panel” or
whatever phrase you use to indicate you&apos;ve gone off-topic will ensure there&apos;s no off-topic
follow-up and help prevent hurt feelings that a silence after might engender.</p>
  <p>If there is a disturbance (like someone continuing to record a session that has been
designated not to allow recording, or there is an unrelenting heckler in the audience), you are
empowered and encouraged to text or voice call the duty ops number +44 7464 379747. You
may also ask the person to leave.</p>

  <h3>Managing Panellists</h3>
  <p>Common difficulties with panellists include talking over others, going off topic, monopolising
the conversation, or otherwise impeding the flow of the discussion. As the moderator, it is your
duty to politely interrupt them and redirect to another panellist, especially if there&apos;s one who
hasn&apos;t had a chance to respond to the question yet. Gentle humour can be a good method for
interrupting if you are good at such things. Alternatively, you can always bring up another topic
with a different panellist as a way to get the rest of the group back into the discussion.</p>
  <p>Don't let panellists (including yourself!) pursue individual agendas unless they are directly on
topic. This includes extended sales pitches for individual author&apos;s books - although brief
mentions of their own work and how it applies to the topic are of course encouraged.</p>
  <p>If a panellist or audience member makes a bigoted statement of some kind, interrupt
immediately and ask another question or move to another panellist. If they continue or do it
again, you can stop letting them participate and can report the incident by text or voice call to
the Duty Listeners number +44 7464 379750.</p>

  <h3>Including the Audience</h3>
  <p>Allow time for questions, but don't let individual audience members monopolise the panel or
drag it away from the topic.</p>
  <p>If the room you are in has a roving mic, remind the audience member to wait for the mic to be
passed to them before they ask their question.</p>
  <p>If your panel is online or in a streamed room at the SEC, you may want to take questions from
online audience members. There will be a volunteer in each streamed room who will be
responsible for relaying questions from Discord.</p>
  <p>Be clear if you are accepting both questions and comments, or just one or the other. If the
audience member has a very long-winded statement or question, interrupting by either asking
the panellists what they think, repeating the gist of the question or comment for the benefit of
the audience, or repeating the gist of it to the audience member and saying “do I have that
right?” and then turning to the panellists can help ensure that more audience members get to
participate in the question time.</p>
  <p>Announce at the beginning of the panel if questions will be at the end or encouraged during
the panel. Have a method for audience members to let you know if they have a question other
than yelling it out (unless that&apos;s the theme of the panel). If people are raising their hands, either
try to remember the order in which they raised them or take notes so you don&apos;t skip people. If
you can think of a way for people who cannot raise their hands to indicate they have a
question, announce this at the beginning as well.</p>
  <p>When you do call on the audience for questions, avoid gendered language (“sir”, “miss”,
“girl/boy”). Don't assume that the audience member can see you pointing at them, either.
Using clothing as identifiers can be a good technique (blue shirt, red corset, TARDIS dress),
but do NOT use assistive devices ("person in the wheelchair") and do not assume that an
unusual outfit is a costume. Avoid judgement-based descriptors like “weird” or “unusual” as
what might be foreign to you may be commonplace in the person's culture - this is a Worldcon
after all!</p>
  <p>While having a general idea of who raised their hand or otherwise signalled, try and make sure
that people from all over the audience get to speak. Try and prioritise people of colour and
non-men to ensure you don&apos;t end up with just white men participating. Just as you want a
diverse panel, you want diverse audience participation. This isn&apos;t to say don&apos;t let white men
speak, but make sure you don&apos;t run out of time before others get to speak also.</p>
  <p>While you should try to keep the programme on track, don't let any of these guidelines cause
you to stop a discussion which is interesting to both the panel and audience! Sometimes
panels end up in different places than were intended, but still fulfil the “big picture” goal of
providing interesting and thought-provoking content.</p>
  <p>If your panel will be on a sensitive topic (writing the other, ask a queer/trans person or the like)
it might be a good idea to ask audience members to submit questions in writing so the
moderator can choose if they should be shared or phrase them more appropriately. Bringing
notecards for this purpose can be helpful.</p>
  <p>If audience members begin asking an insensitive, bigoted, or otherwise offensive question,
even if they don&apos;t realise that&apos;s what they are doing, interrupt and either state that it isn&apos;t
appropriate and move to someone else, or if they&apos;ve asked enough to have a real question,
restate their question more inclusively and ask the panel.</p>
  <p>Do not let the panel become an argument or a fight between the audience and the panel or
between audience members. You are there to guide the conversation, so shut down any
contentious back and forth and move on.</p>

  <h3>Wrapping Up</h3>
  <p>We expect to have programme ops runners notifying you with a sign in the back of the panel
room when you have approximately 10 minutes left in your allotted 60 minute panel, but
please keep an eye on the time in case they aren&apos;t able to make it over to your room. Consider
asking a summing-up question near the end of your allotted time, like asking your panellists
something forward-looking about what changes they see ahead (if applicable) or what they
might be looking forward to regarding the given subject. Then, bring the panel gracefully to a
close when your time is up. Remember to thank everyone!<p>

  <p><em>This moderation guide has been heavily influenced by the moderation guides from Chicon 8,
Capricon, Arisia, and Dublin 2019. We thank them for the use of their content.</em></p>
</article>
<?php
render_footer();
?>