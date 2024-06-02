<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php';

// Class to provide the services for CLYDE
class ClydeService {
  public $provider;
  public $token;

  function __construct() {
    $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'redirectUri'             => ROOT_URL . '/clyde_callback',
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
  function authorize_url() {
    $authUrl = $this->provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $this->provider->getState();
    return $authUrl;
  }

  // From the call back
  function access_token($code, $state) {
    if (empty($code)) {
      throw new Exception('no-code');
    }
    if (empty($state) || ($state !== $_SESSION['oauth2state'])) {
      if (isset($_SESSION['oauth2state'])) {
        unset($_SESSION['oauth2state']);
      }
      throw new Exception('invalid-state');
    }
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
      // Check as to whether the registrant is really attending ...
      // which means looking at the 'product_list_name' as well.
      // Exceptions are: 
      // Teen Attending, Child Attending, Infant Attending, Apocryphal
      // Under 16 Day Tickets

      $under_age = in_array(
        $registrant['product_list_name'], 
        [
          "Teenager", "Children", "Infant",
          "Thu. <16", "Fri. <16", "Sat. <16", "Sun. <16", "Mon. <16", "WkEnd <16"
        ]
      );

      if ($under_age) {
        return 1;
      };

      if ($registrant['product_list_name'] == "Apocryphal") {
        return 2;
      }

      return 0;
    } else {
      return 3;
    }
  }
}

?>
