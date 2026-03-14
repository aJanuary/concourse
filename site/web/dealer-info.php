<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-dealer-info');

render_header(
  'Information for dealers',
  'Information for dealers at ' . CON_NAME . '.',
  ['Home' => '/', 'Information for dealers']
);
?>
<article>
  <h2>Information for dealers</h2>

  <h3><a href="#opening-times" name="opening-times">Opening times</a></h3>
  <p>The Dealers room will be open to convention memebers at the following times:</p>
  <dl class="times-list">
    <div>
      <dt>Friday, 3 April</dt>
      <dd>15:00&ndash;20:00</dd>
    </div>
    <div>
      <dt>Saturday, 4 April</dt>
      <dd>10:00&ndash;18:00</dd>
    </div>
    <div>
      <dt>Sunday, 5 April</dt>
      <dd>10:00&ndash;18:00</dd>
    </div>
    <div>
      <dt>Monday, 6 April</dt>
      <dd>10:00&ndash;14:00</dd>
    </div>
  </dl>

  <p>The room will remain open to Dealers until 4pm to give you time to pack up and get your stock into the loading area.</p>

  <h3><a href="#table-size" name="table-size">Table size</a></h3>
  <p>The tables will be 150 X 75cm (5 X 2.5 ft). This is smaller than expected (180cm/6ft) and the venue were quite late in telling us this, unfortunately. However, it has meant we could adapt out layout to include more dealers and therefore include all of the waiting list. Just be aware of the smaller size when bringing your stock and deciding the layout of the table.</p>

  <h3><a href="#set-up-times" name="set-up-times">Set up times</a></h3>
  <p>Dealers will have access to the venue from 4pm on Thursday 2nd April until 8pm for set up. The room will open again for set up at 9am on Friday 3rd April. The room will be open to general members of the convention at 3pm that day.</p>

  <p>You can drive into Palace loading area to unload your stock and carry it directly into the Dealers room. Hotel staff will be there to direct traffic and keep the loading area clear.</p>

  <h3><a href="#table-allocation" name="table-allocation">Table allocation</a></h3>
  <p>You will be sent an email containing a table allocation. This will be a number for every table you have booked. When you arrive at the venue you just need to find the table(s) with the number(s) you have been assigned.</p>

  <p>We have made an effort to place those who requested access to power in a location where they can do so. However, there are not many sockets in the room, so this was not always possible. When you get your table allocation, this will indicate if you are close to a socket or not.</p>
</article>
<?php
render_footer();
?>
