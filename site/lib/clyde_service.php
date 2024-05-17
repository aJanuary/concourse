<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php';

// Class to provide the services for CLYDE
class ClydeService {
  public $provider;
  public $token;

  function __construct() {
    $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? _SERVER['HTTP_X_FORWARDED_HOST'];
    $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'redirectUri'             => 'https://' . $host . '/clyde',
      'clientId'                => CLYDE_CLIENT_ID,
      'clientSecret'            => CLYDE_CLIENT_SECRET,
      'urlAuthorize'            => CLYDE_SERVER_ENDPOINT . '/oauth/authorize',
      'urlAccessToken'          => CLYDE_SERVER_ENDPOINT . '/api/v1/oauth/token',
      'urlResourceOwnerDetails' => CLYDE_SERVER_ENDPOINT . '/api/v1/me'
    ]);
  }

  // Eventually we want to pass a path here that is
  // passed by via the OAuth state and provides the redirect
  // to where the user really wants to go
  function authorize_url($path = null) {
    // Set the state in the auth request
    $authUrl = $this->provider->getAuthorizationUrl();

    // TODO: check $_SESSION
    // $_SESSION['oauth2state'] = $provider->getState();
    
    // Redirect the browset to the auth URL
    // header('Location: ' . $authUrl);
    return $authUrl;
  }

  // From the call back
  function access_token($code) {
    $this->token = $this->provider->getAccessToken('authorization_code', [
      'code' => $code
    ]);

    return $this->token;
  }

  function get_registrant() {
    $result = $this->provider->getResourceOwner($this->token);
    $as_array = $result->toArray();
    return $as_array['data'];
  }

  function registrant_allowed_access($registrant) {
    if ($registrant['attending_status'] != 'Not Attending') {
      // TODO: put in check as to whether the registrant is really attending ...
      // which means looking at the 'product_list_name' as well
      return true;
    } else {
      return false;
    }
  }
}

?>
