<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Opening times',
  'Opening times for Glasgow 2024',
  ['Home' => '/', 'Opening times']
);
?>
<article>
  <h2>Opening times</h2>
  <ul id="times-toc">
    <li><a href="#accessibility-desk">Accessibility desk</a></li>
    <li><a href="#art-show">Art show</a></li>
    <li><a href="#autographing">Autographing</a></li>
    <li><a href="#business-meeting">Business meeting</a></li>
    <li><a href="#childcare">Childcare</a></li>
    <li><a href="#childrens-programme">Children's programme</a></li>
    <li><a href="#con-ops">Con operations</a></li>
    <li><a href="#convention-bar-1">Convention bar 1</a></li>
    <li><a href="#convention-bar-2">Convention bar 2</a></li>
    <li><a href="#dead-dog-party">Dead dog party</a></li>
    <li><a href="#dealers">Dealers</a></li>
    <li><a href="#exhibits">Exhibits</a></li>
    <li><a href="#green-room-crowne-plaza">Green room, Crowne Plaza</a></li>
    <li><a href="#green-room-sec">Green room, SEC</a></li>
    <li><a href="#in-person-programme">In person programme</a></li>
    <li><a href="#information-desk">Information desk</a></li>
    <li><a href="#listeners-desk">Listeners desk</a></li>
    <li><a href="#masquerade-registration-desk">Masquerade registration desk</a></li>
    <li><a href="#online-help-desk">Online help desk</a></li>
    <li><a href="#online-programme">Online programme</a></li>
    <li><a href="#press-office">Press office</a></li>
    <li><a href="#programme-ops">Programme ops</a></li>
    <li><a href="#programme-participant-check-in">Programme participant check-in</a></li>
    <li><a href="#registration">Registration</a></li>
    <li><a href="#registration-dealers-staff">Registration (dealers & staff only)</a></li>
    <li><a href="#safer-spaces">Safer spaces</a></li>
    <li><a href="#sales-to-members">Sales to members</a></li>
    <li><a href="#site-selection">Site selection</a></li>
    <li><a href="#table-talks">Table talks</a></li>
    <li><a href="#volunteers-desk">Volunteers desk</a></li>
  </ul>

  <h3><a href="#accessibility-desk" name="accessibility-desk">Accessibility desk</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Tuesday, 6 Aug</td>
        <td>11:00&ndash;17:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>11:00&ndash;17:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#art-show" name="art-show">Art show</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>13:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;16:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
    </tbody>
  </table>
    
  <h3><a href="#autographing" name="autographing">Autographing</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>13:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;15:30</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#business-meeting" name="business-meeting">Business meeting</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;13:00</td>
        <td><a href="/resources/maps/village.pdf">The Village, Inspiration Room</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;13:00</td>
        <td><a href="/resources/maps/village.pdf">The Village, Inspiration Room</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;13:00</td>
        <td><a href="/resources/maps/village.pdf">The Village, Inspiration Room</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;13:00</td>
        <td><a href="/resources/maps/village.pdf">The Village, Inspiration Room</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#childcare" name="childcare">Childcare</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>11:00&ndash;17:00</td>
        <td><a href="/resources/maps/sec-campus.pdf">Campanile, Kelvin & Clyde Suites</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>09:30&ndash;17:30</td>
        <td><a href="/resources/maps/sec-campus.pdf">Campanile, Kelvin & Clyde Suites</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>09:30&ndash;17:30</td>
        <td><a href="/resources/maps/sec-campus.pdf">Campanile, Kelvin & Clyde Suites</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>09:30&ndash;23:30</td>
        <td><a href="/resources/maps/sec-campus.pdf">Campanile, Kelvin & Clyde Suites</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>09:30&ndash;17:00</td>
        <td><a href="/resources/maps/sec-campus.pdf">Campanile, Kelvin & Clyde Suites</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#childrens-programme" name="childrens-programme">Children's programme</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>13:00&ndash;17:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Jura, Barra</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Jura, Barra</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Jura, Barra</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Jura, Barra</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;16:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Jura, Barra</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#con-ops" name="con-ops">Con operations</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;00:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse & Crowne Plaza, Rockhall</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>09:00&ndash;00:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse & Crowne Plaza, Rockhall</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>09:00&ndash;00:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse & Crowne Plaza, Rockhall</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>09:00&ndash;00:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse & Crowne Plaza, Rockhall</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>09:00&ndash;20:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse & Crowne Plaza, Rockhall</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#convention-bar-1" name="convention-bar-1">Convention bar 1</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>18:00&ndash;02:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Argyll</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>11:00&ndash;02:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Argyll</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>11:00&ndash;02:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Argyll</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>11:00&ndash;02:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Argyll</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>12:00&ndash;02:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Argyll</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>11:00&ndash;02:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Argyll</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#convention-bar-2" name="convention-bar-2">Convention bar 2</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>11:00&ndash;01:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>11:00&ndash;01:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>11:00&ndash;01:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>11:00&ndash;01:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>12:00&ndash;01:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>11:00&ndash;16:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#dead-dog-party" name="dead-dog-party">Dead dog party</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>19:00&ndash;02:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Argyll</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#dealers" name="dealers">Dealers</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>13:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;16:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#exhibits" name="exhibits">Exhibits</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;16:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#green-room-crowne-plaza" name="green-room-crowne-plaza">Green room, Crowne Plaza</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>12:30&ndash;18:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Balcony</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>09:30&ndash;18:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Balcony</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>09:30&ndash;18:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Balcony</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>09:30&ndash;18:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Balcony</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>09:30&ndash;14:45</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, Balcony</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#green-room-sec" name="green-room-sec">Green room, SEC</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>11:00&ndash;21:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>09:30&ndash;21:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>09:30&ndash;21:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>09:30&ndash;21:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>09:30&ndash;15:45</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#in-person-programme" name="in-person-programme">In person programme</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>11:30&ndash;23:00</td>
        <td>All Venues, All Rooms</td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;23:00</td>
        <td>All Venues, All Rooms</td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;23:00</td>
        <td>All Venues, All Rooms</td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;23:00</td>
        <td>All Venues, All Rooms</td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;15:30</td>
        <td>All Venues, All Rooms</td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#information-desk" name="information-desk">Information desk</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Tuesday, 6 Aug</td>
        <td>11:00&ndash;17:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>11:00&ndash;17:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;16:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#listeners-desk" name="listeners-desk">Listeners desk</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Tuesday, 6 Aug</td>
        <td>11:00&ndash;17:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>11:00&ndash;17:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;16:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#masquerade-registration-desk" name="masquerade-registration-desk">Masquerade registration desk</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;12:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#online-help-desk" name="online-help-desk">Online help desk</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>14:00&ndash;20:00</td>
        <td>Online, Online</td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>09:00&ndash;00:00</td>
        <td>Online, Online</td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>09:30&ndash;00:00</td>
        <td>Online, Online</td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>09:30&ndash;00:00</td>
        <td>Online, Online</td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>09:30&ndash;00:00</td>
        <td>Online, Online</td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>09:30&ndash;18:30</td>
        <td>Online, Online</td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#online-programme" name="online-programme">Online programme</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;00:30</td>
        <td>Online, Online</td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;00:30</td>
        <td>Online, Online</td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;00:30</td>
        <td>Online, Online</td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;00:30</td>
        <td>Online, Online</td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;18:30</td>
        <td>Online, Online</td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#press-office" name="press-office">Press office</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;16:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Hall 5</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#programme-ops" name="programme-ops">Programme ops</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>14:00&ndash;18:30</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>09:00&ndash;18:30</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>09:00&ndash;18:30</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>09:00&ndash;18:30</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>09:00&ndash;18:30</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>09:00&ndash;15:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#programme-participant-check-in" name="programme-participant-check-in">Programme participant check-in</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>10:00&ndash;19:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>09:00&ndash;18:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>09:00&ndash;18:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>09:00&ndash;12:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>12:00&ndash;18:30</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>09:00&ndash;18:30</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>09:00&ndash;15:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Boisdale</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#registration" name="registration">Registration</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>10:00&ndash;19:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>09:00&ndash;19:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>09:00&ndash;19:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>09:00&ndash;19:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>09:00&ndash;19:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>09:00&ndash;14:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#registration-dealers-staff" name="registration-dealers-staff">Registration (dealers & staff only)</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Tuesday, 6 Aug</td>
        <td>14:00&ndash;18:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#safer-spaces" name="safer-spaces">Safer spaces</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>16:00&ndash;20:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, TBA</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, TBA</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, TBA</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, TBA</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;20:00</td>
        <td><a href="/resources/maps/crowne-plaza.pdf">Crowne Plaza, TBA</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#sales-to-members" name="sales-to-members">Sales to members</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>13:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;18:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;16:00</td>
        <td><a href="/resources/maps/hall5.jpg">SEC, Hall 5</a></td>
      </tr>
    </tbody>
  </table>
  
  <h3><a href="#site-selection" name="site-selection">Site selection</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;12:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#table-talks" name="table-talks">Table talks</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>13:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;17:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;15:30</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>

  <h3><a href="#volunteers-desk" name="volunteers-desk">Volunteers desk</a></h3>
  <table class="times-list">
    <tbody>
      <tr>
        <td class="date">Tuesday, 6 Aug</td>
        <td>11:00&ndash;17:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Wednesday, 7 Aug</td>
        <td>11:00&ndash;17:00</td>
        <td><a href="/resources/maps/sec.pdf">SEC, Concourse</a></td>
      </tr>
      <tr>
        <td class="date">Thursday, 8 Aug</td>
        <td>10:00&ndash;19:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Friday, 9 Aug</td>
        <td>10:00&ndash;21:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Saturday, 10 Aug</td>
        <td>10:00&ndash;21:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Sunday, 11 Aug</td>
        <td>10:00&ndash;21:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
      <tr>
        <td class="date">Monday, 12 Aug</td>
        <td>10:00&ndash;16:00</td>
        <td><a href="/resources/maps/hall4.jpg">SEC, Hall 4</a></td>
      </tr>
    </tbody>
  </table>
</article>
<?php
render_footer();
?>