<?php
$GLOBALS['app-data']['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

render_header(
  'Opening times',
  'Opening times for ' . CON_NAME . '.',
  ['Home' => '/', 'Opening times']
);
?>
<article>
  <h2>Opening times</h2>
  <p>The opening times will be updated as we get closer to the event. Please check back regularly for the latest information.</p>
<?php
// Here is an example of how to structure the opening times.
/*
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
    <li><a href="#table-talks">Table talks</a></li>
    <li><a href="#volunteers-desk">Volunteers desk</a></li>
  </ul>

  <h3><a href="#accessibility-desk" name="accessibility-desk">Accessibility desk</a></h3>
  <ul class="times-list">
    <li>
      <p>Tuesday, 6 Aug</p>
      <p>11:00&ndash;17:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>11:00&ndash;17:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>SEC, Hall 4</a></p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>SEC, Hall 4</a></p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>SEC, Hall 4</a></p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>SEC, Hall 4</a></p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>SEC, Hall 4</a></p>
    </li>
  </ul>

  <h3><a href="#art-show" name="art-show">Art show</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>13:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>SEC, Hall 5</p>
    </li>
  </ul>
    
  <h3><a href="#autographing" name="autographing">Autographing</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>13:00&ndash;17:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;17:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;17:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;17:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;15:30</p>
      <p>SEC, Hall 4</p>
    </li>
  </ul>

  <h3><a href="#business-meeting" name="business-meeting">Business meeting</a></h3>
  <ul class="times-list">
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;13:00</p>
      <p>The Village, Inspiration Room</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;13:00</p>
      <p>The Village, Inspiration Room</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;13:00</p>
      <p>The Village, Inspiration Room</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;13:00</p>
      <p>The Village, Inspiration Room</p>
    </li>
  </ul>

  <h3><a href="#childcare" name="childcare">Childcare</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>11:00&ndash;17:00</p>
      <p>Campanile, Kelvin & Clyde Suites</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>09:30&ndash;17:30</p>
      <p>Campanile, Kelvin & Clyde Suites</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>09:30&ndash;17:30</p>
      <p>Campanile, Kelvin & Clyde Suites</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>09:30&ndash;23:30</p>
      <p>Campanile, Kelvin & Clyde Suites</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>09:30&ndash;17:00</p>
      <p>Campanile, Kelvin & Clyde Suites</p>
    </li>
  </ul>

  <h3><a href="#childrens-programme" name="childrens-programme">Children's programme</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>13:00&ndash;17:00</p>
      <p>Crowne Plaza, Jura, Barra</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;17:00</p>
      <p>Crowne Plaza, Jura, Barra</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;17:00</p>
      <p>Crowne Plaza, Jura, Barra</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;17:00</p>
      <p>Crowne Plaza, Jura, Barra</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>Crowne Plaza, Jura, Barra</p>
    </li>
  </ul>
  
  <h3><a href="#con-ops" name="con-ops">Con operations</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>10:00&ndash;00:00</p>
      <p>SEC, Concourse & Crowne Plaza, Rockhall</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>09:00&ndash;00:00</p>
      <p>SEC, Concourse & Crowne Plaza, Rockhall</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>09:00&ndash;00:00</p>
      <p>SEC, Concourse & Crowne Plaza, Rockhall</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>09:00&ndash;00:00</p>
      <p>SEC, Concourse & Crowne Plaza, Rockhall</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>09:00&ndash;20:00</p>
      <p>SEC, Concourse & Crowne Plaza, Rockhall</p>
    </li>
  </ul>

  <h3><a href="#convention-bar-1" name="convention-bar-1">Convention bar 1</a></h3>
  <ul class="times-list">
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>18:00&ndash;02:00</p>
      <p>Crowne Plaza, Argyll</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>11:00&ndash;02:00</p>
      <p>Crowne Plaza, Argyll</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>11:00&ndash;02:00</p>
      <p>Crowne Plaza, Argyll</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>11:00&ndash;02:00</p>
      <p>Crowne Plaza, Argyll</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>12:00&ndash;02:00</p>
      <p>Crowne Plaza, Argyll</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>11:00&ndash;02:00</p>
      <p>Crowne Plaza, Argyll</p>
    </li>
  </ul>

  <h3><a href="#convention-bar-2" name="convention-bar-2">Convention bar 2</a></h3>
  <ul class="times-list">
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>11:00&ndash;01:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>11:00&ndash;01:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>11:00&ndash;01:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>11:00&ndash;01:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>12:00&ndash;01:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>11:00&ndash;16:00</p>
      <p>SEC, Hall 4</p>
    </li>
  </ul>

  <h3><a href="#dead-dog-party" name="dead-dog-party">Dead dog party</a></h3>
  <ul class="times-list">
    <li>
      <p>Monday, 12 Aug</p>
      <p>19:00&ndash;02:00</p>
      <p>Crowne Plaza, Argyll</p>
    </li>
  </ul>

  <h3><a href="#dealers" name="dealers">Dealers</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>13:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>SEC, Hall 5</p>
    </li>
  </ul>

  <h3><a href="#exhibits" name="exhibits">Exhibits</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>SEC, Hall 4</p>
    </li>
  </ul>
  
  <h3><a href="#green-room-crowne-plaza" name="green-room-crowne-plaza">Green room, Crowne Plaza</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>12:30&ndash;18:00</p>
      <p>Crowne Plaza, Balcony</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>09:30&ndash;18:00</p>
      <p>Crowne Plaza, Balcony</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>09:30&ndash;18:00</p>
      <p>Crowne Plaza, Balcony</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>09:30&ndash;18:00</p>
      <p>Crowne Plaza, Balcony</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>09:30&ndash;14:45</p>
      <p>Crowne Plaza, Balcony</p>
    </li>
  </ul>

  <h3><a href="#green-room-sec" name="green-room-sec">Green room, SEC</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>11:00&ndash;21:00</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>09:30&ndash;21:00</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>09:30&ndash;21:00</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>09:30&ndash;21:00</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>09:30&ndash;15:45</p>
      <p>SEC, Boisdale</p>
    </li>
  </ul>
  
  <h3><a href="#in-person-programme" name="in-person-programme">In person programme</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>11:30&ndash;23:00</p>
      <p>All Venues, All Rooms</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;23:00</p>
      <p>All Venues, All Rooms</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;23:00</p>
      <p>All Venues, All Rooms</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;23:00</p>
      <p>All Venues, All Rooms</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;15:30</p>
      <p>All Venues, All Rooms</p>
    </li>
  </ul>

  <h3><a href="#information-desk" name="information-desk">Information desk</a></h3>
  <ul class="times-list">
    <li>
      <p>Tuesday, 6 Aug</p>
      <p>11:00&ndash;17:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>11:00&ndash;17:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>SEC, Hall 4</p>
    </li>
  </ul>

  <h3><a href="#listeners-desk" name="listeners-desk">Listeners desk</a></h3>
  <ul class="times-list">
    <li>
      <p>Tuesday, 6 Aug</p>
      <p>11:00&ndash;17:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>11:00&ndash;17:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>10:00&ndash;19:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;19:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;19:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;19:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>SEC, Hall 4</p>
    </li>
  </ul>

  <h3><a href="#masquerade-registration-desk" name="masquerade-registration-desk">Masquerade registration desk</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;12:00</p>
      <p>SEC, Hall 4</p>
    </li>
  </ul>

  <h3><a href="#online-help-desk" name="online-help-desk">Online help desk</a></h3>
  <ul class="times-list">
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>14:00&ndash;20:00</p>
      <p>Online, Online</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>09:00&ndash;00:00</p>
      <p>Online, Online</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>09:30&ndash;00:00</p>
      <p>Online, Online</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>09:30&ndash;00:00</p>
      <p>Online, Online</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>09:30&ndash;00:00</p>
      <p>Online, Online</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>09:30&ndash;18:30</p>
      <p>Online, Online</p>
    </li>
  </ul>
  
  <h3><a href="#online-programme" name="online-programme">Online programme</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>10:00&ndash;00:30</p>
      <p>Online, Online</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;00:30</p>
      <p>Online, Online</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;00:30</p>
      <p>Online, Online</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;00:30</p>
      <p>Online, Online</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;18:30</p>
      <p>Online, Online</p>
    </li>
  </ul>
  
  <h3><a href="#press-office" name="press-office">Press office</a></h3>
  <ul class="times-list">
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>SEC, Hall 5</p>
    </li>
  </ul>

  <h3><a href="#programme-ops" name="programme-ops">Programme ops</a></h3>
  <ul class="times-list">
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>14:00&ndash;18:30</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>09:00&ndash;18:30</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>09:00&ndash;18:30</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>09:00&ndash;18:30</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>09:00&ndash;18:30</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>09:00&ndash;15:00</p>
      <p>SEC, Boisdale</p>
    </li>
  </ul>
  
  <h3><a href="#programme-participant-check-in" name="programme-participant-check-in">Programme participant check-in</a></h3>
  <ul class="times-list">
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>10:00&ndash;19:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>09:00&ndash;18:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>09:00&ndash;18:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>09:00&ndash;12:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>12:00&ndash;18:30</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>09:00&ndash;18:30</p>
      <p>SEC, Boisdale</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>09:00&ndash;15:00</p>
      <p>SEC, Boisdale</p>
    </li>
  </ul>
  
  <h3><a href="#registration" name="registration">Registration</a></h3>
  <ul class="times-list">
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>10:00&ndash;19:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>09:00&ndash;19:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>09:00&ndash;19:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>09:00&ndash;19:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>09:00&ndash;19:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>09:00&ndash;14:00</p>
      <p>SEC, Concourse</p>
    </li>
  </ul>
  
  <h3><a href="#registration-dealers-staff" name="registration-dealers-staff">Registration (dealers & staff only)</a></h3>
  <ul class="times-list">
    <li>
      <p>Tuesday, 6 Aug</p>
      <p>14:00&ndash;18:00</p>
      <p>SEC, Concourse</p>
    </li>
  </ul>
  
  <h3><a href="#safer-spaces" name="safer-spaces">Safer spaces</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>16:00&ndash;20:00</p>
      <p>Crowne Plaza, TBA</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>Crowne Plaza, TBA</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>Crowne Plaza, TBA</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>Crowne Plaza, TBA</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;20:00</p>
      <p>Crowne Plaza, TBA</p>
    </li>
  </ul>

  <h3><a href="#sales-to-members" name="sales-to-members">Sales to members</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>13:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;18:00</p>
      <p>SEC, Hall 5</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>SEC, Hall 5</p>
    </li>
  </ul>
  
  <h3><a href="#table-talks" name="table-talks">Table talks</a></h3>
  <ul class="times-list">
    <li>
      <p>Thursday, 8 Aug</p>
      <p>13:00&ndash;17:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;17:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;17:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;17:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;15:30</p>
      <p>SEC, Hall 4</p>
    </li>
  </ul>

  <h3><a href="#volunteers-desk" name="volunteers-desk">Volunteers desk</a></h3>
  <ul class="times-list">
    <li>
      <p>Tuesday, 6 Aug</p>
      <p>11:00&ndash;17:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Wednesday, 7 Aug</p>
      <p>11:00&ndash;17:00</p>
      <p>SEC, Concourse</p>
    </li>
    <li>
      <p>Thursday, 8 Aug</p>
      <p>10:00&ndash;19:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Friday, 9 Aug</p>
      <p>10:00&ndash;21:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Saturday, 10 Aug</p>
      <p>10:00&ndash;21:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Sunday, 11 Aug</p>
      <p>10:00&ndash;21:00</p>
      <p>SEC, Hall 4</p>
    </li>
    <li>
      <p>Monday, 12 Aug</p>
      <p>10:00&ndash;16:00</p>
      <p>SEC, Hall 4</p>
    </li>
  </ul>
*/ ?>
</article>
<?php
render_footer();
?>