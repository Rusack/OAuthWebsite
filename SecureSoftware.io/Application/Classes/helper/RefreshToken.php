<?php
namespace Helper;

use Helper\SessionManager;
use Helper\ApiCaller;

class RefreshToken{

	// Get a new token if necessary
	static function checkValidity(){

	    SessionManager::sessionStart('user');


		if($_SESSION['expires'] < time()){
			$token = json_decode(apiCaller::apiRequest("http://todolistapi.io/Oauth/Token",
			    array(
				'client_id' => "todolist",
				'client_secret' => "todolist",
				'redirect_uri' => 'http://securesoftware.io/code',
				'state' => $_SESSION['state'],
				'grant_type' => 'refresh_token',
				'refresh_token' => $_SESSION['refresh_token']
				)));
		      $_SESSION['access_token'] = $token->access_token;
		      $_SESSION['expires'] = $token->expires;
		      $_SESSION['refresh_token'] =  $token->refresh_token;
		    }
	}

}
