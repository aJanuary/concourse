<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');

check_permission('manage-discord-ids');

render_header("Manage discord ids", "Manage discord ids.");
?>

<a href="/" class="back">&lt; Back to member portal</a>

<article>
  <h3>Manage discord ids</h3>
  
  <table>
    <thead>
      <tr>
        <th>Badge no</th>
        <th>Name</th>
        <th>Discord ID</th>
        <th>Discord username</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $members = db_get_member_discord_data();
      foreach ($members as $member) {
        $rowspan = max(count($member['discord_ids']), 1);
      ?>
      <tr class="new-badgeno-row">
        <td rowspan=<?php echo $rowspan; ?>><?php echo $member['badge_no']; ?></td>
        <td rowspan=<?php echo $rowspan; ?>><?php echo $member['name']; ?></td>
        <?php
        if (count($member['discord_ids']) == 0) {
        ?>
            <td colspan=2><em>No discord id</em></td>
          </tr>
        <?php
        } else {
          foreach ($member['discord_ids'] as $i => $discord_id) {
            if ($i > 0) {
            ?>
            <tr>
            <?php
            }
            ?>
            <td><?php echo $discord_id['id']; ?></td>
            <td><?php echo $discord_id['username']; ?></td>
          </tr>
          <?php
          }
        }
      }
      ?>
    </tbody>
  </table>

</article>

<?php
render_footer();
?>