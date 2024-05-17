<?php
/*
 * Provide a GET request for OAuth login with Clyde (Glasgow Reg)
 */
require_once(getenv('CONFIG_LIB_DIR') . '/clyde_service.php');

$clyde = new ClydeService();

// NOTE: authorize_url returns the auth URL, add path parameter to pass back
// in the OAuth flow to "remember" where to redirect to after authentication.
header('Location: ' . $clyde->authorize_url());

?>
