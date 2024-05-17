<?php
require_once(getenv('CONFIG_LIB_DIR') . '/config.php');
require_once getenv('CONFIG_LIB_DIR') . '/vendor/autoload.php';

# Table name: oauth_identities
#
#  id           :uuid             not null, primary key
#  email        :string
#  lock_version :integer
#  provider     :string
#  raw_info     :jsonb            not null
#  reg_number   :string
#  created_at   :datetime         not null
#  updated_at   :datetime         not null
#  reg_id       :string
#

class ClydeService {
  public $provider;
  public $token;

  function __construct() {
    $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
      'redirectUri'             => 'https://g24portal.loophole.site/clyde',
      'clientId'                => CLYDE_CLIENT_ID,
      'clientSecret'            => CLYDE_CLIENT_SECRET,
      'urlAuthorize'            => CLYDE_SERVER_ENDPOINT . '/oauth/authorize',
      'urlAccessToken'          => CLYDE_SERVER_ENDPOINT . '/api/v1/oauth/token',
      'urlResourceOwnerDetails' => CLYDE_SERVER_ENDPOINT . '/api/v1/me'
    ]);
  }

  // 
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
