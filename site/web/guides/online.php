<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-participant-guides');

render_header(
  'Online participant guide',
  'Online participant guide for Glasgow 2024',
  ['Home' => '/', 'Guides' => '/guides/list', 'Online participant guide']
);
?>
<article class="instructions">
  <h2>Online participant guide</h2>
  <p>This document provides guidance for all panellists who are on an online panel or who are
joining an in-person (hybrid) panel from the internet.</p>
  <p>It is also relevant to presenters who are presenting online-only.</p>
  <p>NOTE: The links mentioned below, which you will use to join your panel, won't be visible in Planorama before the convention opens.</p>
  <h3>If you are a panellist on an online-only panel or presentation</h3>
  <ul>
    <li>You will need a device (phone, tablet, laptop, desktop) with internet access, a camera
(built in or external) and a microphone. It is recommended that you use Chrome, Firefox
or Edge as your browser.<br>You do not need to appear on camera if you are not comfortable doing so.</li>
    <li>To get the Streamyard link for your panel or presentation, log in to <a href="https://planorama.glasgow2024.org">Planorama</a> on this
device and check your schedule for the Join link next to the programme item.</li>
    <li>About 20-30 minutes before the start time, click on the link. This will connect you to your
item with enough time for a pre-panel green-room style discussion with the other
participants, and a technical check.
      <ol>
        <li>Your device will ask you to accept cookies for Streamyard. Click Yes.</li>
        <li>Depending on your settings, you may be asked to allow mic/cam access. Click Allow mic/cam access.</li>
        <li>You will see a “Let&apos;s check your camera and mic” screen.</li>
        <li>Check that the microphone is not on Mute, and that the Video option is on.</li>
        <li>Fill in your preferred Display Name (preferably your “Badge Name”) and, if desired, an optional “Headline.”</li>
        <li>Click Enter Studio.</li>
      </ol>
    <li>Once you have joined Streamyard, You should see your Streamyard Room Host, who will help you check your settings, and talk you through the rest of the process.</li>
  </ul>
  <p>Please also watch a video of someone joining Streamyard in our <a href="https://www.youtube.com/playlist?app=desktop&list=PLFa7TsNVW8n8qJGOpXfhQPogX02MjYem_">online convention playlist</a>.</p>

  <h4>If you are on-site in Glasgow and taking part in an online panel</h4>
  <p>We have a quiet room available in the Crowne Plaza Hotel which you can use to connect to your
panel using hotel wifi. We also have some headsets which are sanitised after each use, in case
you do not have your own.</p>
  <p>Please ask in advance with Programme Ops or a Green Room to make sure the room will be
available - we suggest at least a couple of hours before your item. Before using the room, check
in at Crowne Plaza Green Room.</p>

  <h4>If you are an online panellist on a (mainly) in-person panel</h4>
  <p>(This is referred to in Planorama as a hybrid panel.)</p>
  <ul>
    <li>You will need a device (phone, tablet, laptop, desktop) with internet access, a camera
(built in or external) and a microphone (built in or external). In general, the bigger the
screen size the easier you will find it. You do NOT need headphones, but you may find it
easier.<br>You do not need to appear on camera if you are not comfortable doing so.</li>
    <li>To get the Zoom link for your panel, log in to Planorama on this device and check your
schedule for the Join link next to the programme item.</li>
    <li>About 30-40 minutes before the start time, click on the link. This will connect you to your
item with enough time for a pre-panel discussion with the other participants, and a technical check.
    <ol>
      <li>If you have Zoom installed, your device will ask you for permission to open Zoom.
If not, you will be taken to the web version of Zoom.</li>
      <li>Depending on your settings, you may be asked to allow Zoom access to your
camera and/or microphone.<br>Check that the microphone is not on Mute, and that the Video option is on.</li>
    </ol>
    <li>Once you&apos;re in the Zoom, you should see your Green Room Host, who will help you
check your settings, and talk you through the rest of the process.</li>
  </ul>
  <p>Please also watch a video of someone joining via Zoom in our <a href="https://www.youtube.com/playlist?app=desktop&list=PLFa7TsNVW8n8qJGOpXfhQPogX02MjYem_">online convention playlist</a>.</p>

  <h3>General information</h3>
  <p>You can find overall online information in our <a href="https://glasgow2024.org/online-convention/member-portal/online-convention-faq/">online FAQ</a>.</p>
  <p>Note that you do not need to be logged in to the Ring Central Events (RCE) system to join your own panel. RCE is for viewing panels, not joining them.</p>
  <p>If you would like to attend a training session to practise joining an online panel, we will be hosting two sessions for panellists. To find out when this is in your timezone, you can use this <a href="https://www.timeanddate.com/worldclock/converter.html">timezone converter</a>.</p>
  <p>Topic: Training/support for panellists<br>
  Time: Aug 4, 2024 03:00 AM London/Glasgow<br>
<a href="https://us02web.zoom.us/j/89568191085?pwd=onxFyA7UmBlURpOH5TPOJMIG1bLdoy.1">https://us02web.zoom.us/j/89568191085?pwd=onxFyA7UmBlURpOH5TPOJMIG1bLdoy.1</a><br>
Meeting ID: 895 6819 1085<br>
Passcode: 864755</p>

  <p>Topic: Training/support for panellists<br>
Time: Aug 4, 2024 05:00 AM London/Glasgow<br>
Join Zoom Meeting<br>
<a href="https://us02web.zoom.us/j/82557180912?pwd=2H2ZgD0maPryPmLHd1yiakQ19znsjn.1">https://us02web.zoom.us/j/82557180912?pwd=2H2ZgD0maPryPmLHd1yiakQ19znsjn.1</a><br>
Meeting ID: 825 5718 0912<br>
Passcode: 933159</p>

  <p>If you would like to attend a training session but cannot make the times above, please contact
<a href="mailto:online-convention-tech@glasgow2024.org">online-convention-tech@glasgow2024.org</a>.</p>

  <p>If you need assistance during the con, you can ask in the help desk channel in Discord, email
<a href="mailto:online-convention-help@glasgow2024.org">online-convention-help@glasgow2024.org</a>, or visit the info desk in RCE. If you are on site, you
can also visit our in-person help desk co-located with Programme Ops in Boisdale 2 in the Loch suite in the SEC.</p>
</article>
<?php
render_footer();
?>