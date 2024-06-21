<?php
$_REQUEST['allow_implicit_anonymous'] = true;
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/template.php');
require_once(getenv('CONFIG_LIB_DIR') . '/clyde_service.php');

if (array_key_exists('check_has_joined_discord', $_GET)) {
  $usernames = db_get_discord_usernames($_COOKIE['session']);
  echo !empty($usernames) ? 'true' : 'false';
  exit;
}

if (array_key_exists('item_id', $_GET)) {
  $item_id = $_GET['item_id'];
  $post = db_get_discord_post($item_id);

  if (!$post['post_url']) {
    render_header("Go to a Discord chat for an item", "Unknown item in the Glasgow 2024 Discord.");
    ?>
    <a href="/" class="back">&lt; Back to member portal</a>

    <article>
      <h2>Unknown item</h2>
      <p>Sorry, we don&apos;t know about that item.</p>
    </article>
    <?php
    render_footer();
    exit;
  }
} else if (array_key_exists('room_id', $_GET)) {
  $room_id = $_GET['room_id'];
  $post = db_get_discord_post_by_room_and_time($room_id, time());

  if (!$post['post_url']) {
    render_header("Go to a Discord chat for an item", "Nothing scheduled in that room in the Glasgow 2024 Discord.");
    ?>
    <a href="/" class="back">&lt; Back to member portal</a>

    <article>
      <h2>Nothing scheduled</h2>
      <p>We don&apos;t think anything is happening in that room at the moment. Check back later.</p>
    </article>
    <?php
    render_footer();
    exit;
  }
} else {
  die('Missing required query parameter "item_id" or "room_id"');
}

$usernames = db_get_discord_usernames($_COOKIE['session']);
if (!empty($usernames)) {
  header('Location: ' . $post['post_url']);
  exit;
}

$invite_url = 'https://discord.com/oauth2/authorize?client_id=' . DISCORD_CLIENT_ID . '&response_type=code&redirect_uri=' . urlencode(ROOT_URL) . '%2Fchat_callback&scope=identify';

render_header("Go to a Discord chat for an item", "Join the Glasgow 2024 Discord server.");

$clyde = new ClydeService();
$_SESSION['oauth2redirect'] = $_SERVER['REQUEST_URI'];

?>
  <a href="/" class="back">&lt; Back to member portal</a>

  <article>
    <h2>Join the Discord</h2>
    <p>Before we can take you to the Discord conversation for this item, you'll need to join the Discord.</p>
    <div class="wizard">
    <section id="step1">
      <header class="<?php echo is_logged_in() ? 'complete' : 'active' ?>"><h3>Step 1 - Log in to the member portal</h3></header>
      <?php
      if (!is_logged_in()) {
      ?>
        <div class="content">
          <a href="<?php echo $clyde->authorize_url(); ?>" class="button login">Log in with Glasgow Registration</a>
          <p><a href="/login-instructions" target="_blank">Log in instructions</a>
          <p>If you are having trouble logging in, please e-mail <a href="mailto:<?php echo EMAIL; ?>?subject=Trouble logging in to member portal"><?php echo EMAIL; ?></a>.</p>
      </div>
      <?php
      }
      ?>
    </section>
    <section id="step2">
      <header class="<?php echo is_logged_in() ? "active": "pending"; ?>"><h3>Step 2 - Join the Discord</h3></header>
      <div class="content">
        <p><a class="button <?php echo is_logged_in() ? "" : "disabled"; ?>" target="_blank" <?php echo is_logged_in() ? 'href="' . $invite_url . '"' : ''; ?>>Join the Discord server</a></p>
        <p><a href="/discord-instructions" target="_blank">Instructions on how to Join the discord</a></p>
      </div>
    </section>
    <section id="step3">
      <header class="pending"><h3>Step 3 - Go to the item</h3></header>
      <div class="content">
        <p><a id="deeplink-button" class="button disabled">Go to the item in Discord</a></p>
      </div>
    </section>
  </article>
  <script>
    function checkJoinedDiscord() {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', '/deep-link/chat?check_has_joined_discord=true', true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          if (xhr.responseText === 'true') {
            const $step2 = document.getElementById('step2');
            const $step2Content = $step2.getElementsByClassName('content')[0];
            const $step2Header = $step2.getElementsByTagName('header')[0];
            $step2.removeChild($step2Content);
            $step2Header.classList.remove('active');
            $step2Header.classList.add('complete');

            const $step3 = document.getElementById('step3');
            const $step3Header = $step3.getElementsByTagName('header')[0];
            $step3Header.classList.remove('pending');
            $step3Header.classList.add('active');

            const deepLinkButton = document.getElementById('deeplink-button');
            deepLinkButton.classList.remove('disabled');
            deepLinkButton.href = '<?php echo $post['post_url']; ?>';
            deepLinkButton.title = '';
          } else {
            setTimeout(checkJoinedDiscord, 1000);
          }
        }
      };
      xhr.send();
    }
    checkJoinedDiscord();
  </script>
<?php
render_footer();
?>