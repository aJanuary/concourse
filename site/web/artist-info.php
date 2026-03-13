<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('see-artist-info');

render_header(
  'Information for artists',
  'Information for artists at ' . CON_NAME . '.',
  ['Home' => '/', 'Information for artists']
);
?>
<article>
  <h2>Information for artists</h2>
  <ul>
    <li><a href="#move-in-out">Move in/move out</a></li>
    <li><a href="#loading">Loading and unloading</a></li>
    <li><a href="#space">Space and layout</a></li>
    <li><a href="#displaying">Displaying your work</a></li>
    <li><a href="#admin">Control sheets</a></li>
    <li><a href="#rules">Rules</a></li>
  </ul>

  <h3><a href="#move-in-out" name="move-in-out">Move in/move out</a></h3>
  <dl class="times-list">
    <div>
      <dt>Friday, 3 April</dt>
      <dd>09:00&ndash;13:00 (artist set-up)</dd>
      <dd>15:00&ndash;17:00 (previews)</dd>
      <dd>17:00&ndash;20:00</dd>
    </div>
    <div>
      <dt>Saturday, 4 April</dt>
      <dd>10:00&ndash;18:00</dd>
    </div>
    <div>
      <dt>Sunday, 5 April</dt>
      <dd>10:00&ndash;16:00</dd>
      <dd>16:00&ndash;18:00 (collection of purchased work)</dd>
    </div>
    <div>
      <dt>Monday, 6 April</dt>
      <dd>09:00&ndash;11:00 (artists' collection of unsold work)</dd>
    </div>
  </dl>

  <h3><a href="#loading" name="loading">Loading and unloading</a></h3>
  <p>The art show is in <a href="https://map.eastercon2026.org/room/princes" target="_blank">Princes</a> and access to the art show will be via <a href="https://map.eastercon2026.org/room/main-entrance" target="_blank">the main entrance to the hotel</a>. If you think this may be difficult for you, just let us know and we will find you some help on the day.</p>

  <h3><a href="#space" name="space">Space and layout</a></h3>
  <p>Boards will be in a single bay. If you have both a table and boards we will put them as close together as possible &mdash; however if you are also selling Jewellery your table space will be placed close to the sales desk for security reasons.</p>

  <h3><a href="#displaying" name="displaying">Displaying your work</a></h3>
  <p>Please let us know if you have any specific requirements regarding setting up/displaying your work, or if you have work which is particularly heavy or awkward to hang. The artwork is hung from pegboard hooks, and for framed work we strongly recommend using D rings or screw eyes plus cord or wire. The flat bars supplied with some frames do not fit over the hooks, nor do the type with little rings at the top. Unframed work can be hung as long as it is protected by a cellophane bag and backing board. SImilarly prints in the printshop need to be in a cellophane bag + backing board so they do not get damaged. If you have table space you are welcome to bring stands, battery powered lights etc to display your work.</p>

  <h3><a href="#admin" name="admin">Control sheets</a></h3>
  <p>Control sheets for your work are linked below. There is no auction at this convention, so price your work accordingly.</p>
  <ul>
    <li>Control sheet<br><a href="/resources/artshow/Iridescence Control Sheet.xlsx" download>[Excel spreadsheet]</a> <a href="/resources/artshow/Iridescence Control Sheet.pdf" download>[PDF]</a></li>
    <li>Printshop control sheet<br><a href="/resources/artshow/Iridescence Printshop Control Sheet.xlsx" download>[Excel spreadsheet]</a> <a href="/resources/artshow/Iridescence Printshop Control Sheet.pdf" download>[PDF]</a></li>
    <li>Labels<br><a href="/resources/artshow/Iridescence Artshow labels.xlsx" download>[Excel spreadsheet]</a> <a href="/resources/artshow/Iridescence Artshow labels.pdf" download>[PDF]</a></li>
  </ul>
  <p>If you can complete and email the control sheets to <a href="mailto:artshow@eastercon2026.org">artshow@eastercon2026.org</a> before the convention (ideally a few days before), preferably in spreadsheet form, it would be enormously helpful. There is no problem with amending them on the day but it gives us a head start on the paperwork. We also need you to bring a paper copy of your control sheet. We have sent you labels if you'd like to fill those out in advance, but we will also have blank ones at the convention. We have linked to the sheets and labels as an Excel file and also as a PDF which you could fill out by hand. If you don't have Excel you can open the spreadsheet in Google Sheets. To ensure that you are paid for sales as efficiently as possible, please provide your payment details (see 17 of the <a href="#rules">Art Show rules</a>) in advance of the con to the Treasurer, Phil Nanson at <a href="mailto:finance@eastercon2026.org">finance@eastercon2026.org</a>. Please do not send those details to art show.</p>

  <h3><a href="#rules" name="rules">Rules</a></h3>
  <ol>
    <li>All artists exhibiting in the art show must have an attending membership of the convention.</li>
    <li>All artwork in the show must be consistent with the convention&apos;s <a href="https://eastercon2026.org/about/code-of-conduct">Code of Conduct</a>.</li>
    <li>The convention does not provide insurance for artworks being exhibited. Artists may wish to obtain their own exhibition insurance.</li>
    <li>All artwork in the show must be exhibited with a label showing the title of the work, medium &amp; price (or NFS). For digital work, please indicate the programme used. AI art is not permitted. An additional label on the back of the work can also be helpful where this is practical.</li>
    <li>All artists must complete a control sheet giving a complete list of the works they submit to the main show. A separate control sheet will be required for work in the print shop. It would be very helpful if a draft control sheet could be provided in advance of the convention; changes can still be made on the day.</li>
    <li>Please also indicate on the control sheet whether or not you consent to your work being removed from the art show before the end of the convention if purchased by a person leaving the convention early. Please also indicate whether or not you consent to work being photographed &amp; videoed by art show staff to promote the art show at the convention.</li>
    <li>Artists are expected to hang their panels on Friday morning, when the room will be open to artists only. Please contact the Art Show Leads (Jackie and Tim Duckworth) if you have specific requirements.</li>
    <li>Artists (or their representatives) must hang/install their art themselves. If any art falls from the panels during the show, Art Show staff will restore it to its place, unless the artist has left other instructions.</li>
    <li>The art show will provide all materials (hooks etc) for hanging, unless special requests are made in advance and approved by the Art Show Lead. Please do not bring blue tack, nails, screws, Velcro or tape to use in hanging your work on the panels. Pictures should be ready to hang with string or wire on the back. Pozzi hangers and similar do not work with the pegboard hooks.</li>
    <li>Artwork may not obstruct passageways or alcoves within the show unless agreed with the Art Show Lead.</li>
    <li>Once your artwork is in place and before you leave the art show, you must get the art show staff to verify that all items are listed on your control sheets and that prices on the control sheet are consistent with labels.</li>
    <li>There will be a print shop area for the sale of prints, cards etc. Greetings cards must all be the same price. Prints must be protected by a sleeve, and have a backing board if they are to be placed in a print rack. Please make sure a price label is attached to all work in the print shop, and that the name of the artist is clearly displayed. Sales from the print shop will be taken away by the purchaser immediately.</li>
    <li>Artists may not remove their work from the main show before the end of the show without special permission from the Art Show Lead.</li>
    <li>There will be no Art Auction. Work in the show will be for sale at the price shown on the label. Please bear this in mind when pricing your work.</li>
    <li>Artists must collect unsold work from the art show on Monday morning, unless otherwise agreed. (Sold works will be available for collection by purchasers on Sunday afternoon and evening.) If any work is uncollected at the end of the convention it may be returned to you at your expense.</li>
    <li>All artists must provide full contact details to the art show, including a means of contacting them during the show.</li>
    <li>Artists must provide payment details to the convention Treasurer so they can be paid by BACS for any work sold. This should include bank account name, bank name, sort code &amp; account number (or IBAN number &amp; BIC codes for international payments). Anyone wishing to be paid by cheque will need to arrange this with the Treasurer. Payments will be made as soon as possible after the convention. Please note that payments will not be made to artists at the convention - in cash or in any other form.</li>
  </ol>
  <p>Any queries about these rules must be sent to the Art Show Leads, Jackie and Tim Duckworth (<a href="mailto:artshow@eastercon2026.org">artshow@eastercon2026.org</a>).</p>
</article>
<?php
render_footer();
?>
