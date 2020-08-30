<?php

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Microsoft\Graph\Core\GraphConstants;


class AuthController extends Controller
{
   /* public $oauth_env = array(
        'OAUTH_APP_ID' => 'cc492ce4-646f-48a9-96be-421dae38dbf3',
        'OAUTH_APP_PASSWORD'=>'LdJ.c.Uu_56Uh54YK_K_3YX7lP2F~_v0qL',
        'OAUTH_REDIRECT_URI'=>'http://localhost/msgraph/Auth/callback',
        'OAUTH_SCOPES'=>'offline_access https://graph.microsoft.com/.default',
        'OAUTH_AUTHORIZE_ENDPOINT'=>'https://login.microsoftonline.com/0bab8eb0-b94d-4992-aa17-04064768c392/oauth2/v2.0/authorize',
        'OAUTH_TOKEN_ENDPOINT'=>'https://login.microsoftonline.com/0bab8eb0-b94d-4992-aa17-04064768c392/oauth2/v2.0/token',
    );*/
    
    public function sign(){
        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => $_ENV['OAUTH_APP_ID'],
            'clientSecret'            => $_ENV['OAUTH_APP_PASSWORD'],
            'redirectUri'             => $_ENV['OAUTH_REDIRECT_URI'],
            'urlAuthorize'            => $_ENV['OAUTH_AUTHORIZE_ENDPOINT'],
            'urlAccessToken'          => $_ENV['OAUTH_TOKEN_ENDPOINT'],
            'urlResourceOwnerDetails' => '',
            'scopes'                  => $_ENV['OAUTH_SCOPES']
        ]);
        $authUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in callback
        $_SESSION['oauthState'] = $oauthClient->getState();

		// Redirect to AAD signin page
		header("Location: $authUrl");
        
    }
 
    public function callback($params){
        // Validate state
        $expectedState = $_SESSION['oauthState'];
		$providedState = $params['state'];

	    unset($_SESSION['oauthState']);       

        if (!isset($expectedState) || !isset($providedState) || $expectedState != $providedState) {
			header('Location: /msgraph/Auth/sign');
			$_SESSION['errors'] = 'The provided auth state did not match the expected value';
			
        }
  
        // Authorization code should be in the "code" query param
        $authCode = $params['code'];
        if (isset($authCode)) {
            // Initialize the OAuth client
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId'                => $_ENV['OAUTH_APP_ID'],
                'clientSecret'            => $_ENV['OAUTH_APP_PASSWORD'],
                'redirectUri'             => $_ENV['OAUTH_REDIRECT_URI'],
                'urlAuthorize'            => $_ENV['OAUTH_AUTHORIZE_ENDPOINT'],
                'urlAccessToken'          => $_ENV['OAUTH_TOKEN_ENDPOINT'],
                'urlResourceOwnerDetails' => '',
                'scopes'                  => $_ENV['OAUTH_SCOPES']
            ]);
    
            try {
                // Make the token request
                $accessToken = $oauthClient->getAccessToken('authorization_code', ['code' => $authCode]);
            
                $graph = new Graph();
                $graph->setAccessToken($accessToken->getToken());
            
                $user = $graph->createRequest('GET', '/me')
                    ->setReturnType(Model\User::class)
                    ->execute();		
		
            
                $tokenCache = new TokenCache();
                $tokenCache->storeTokens($accessToken, $user);

				
			    header('Location: /msgraph/User/display');

            }
            catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
				$this->render("index");
				$_SESSION['errors'] = $e->getMessage();
               
            }
        }		
    }
	
    public function signout(){
        $tokenCache = new TokenCache();
        $tokenCache->clearTokens();
        $this->render("index");
    }

}
