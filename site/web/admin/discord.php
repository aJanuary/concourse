<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

check_permission('manage-discord-ids');

render_page('pages/admin/discord.html.twig', [
  'members' => db_get_member_discord_data(),
]);
