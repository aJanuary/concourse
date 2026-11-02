<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once(getenv('CONFIG_LIB_DIR') . '/session_auth.php');
require_once(getenv('CONFIG_LIB_DIR') . '/twig.php');

// Square sends members here after they pay. Their voter key is assigned when
// Square's webhook arrives, which may be a little later, so this waits for it.
check_permission('see-site-selection');

render_page('pages/site-selection/processing.html.twig');
