<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Model\UserModel;
use Helper\SessionManager;
use Helper\tokenManager;


// Authorization page
$app->get('/Oauth/Authorize', function (Request $request, Response $response, $args) use ($server){

	SessionManager::sessionStart('user');
   

    $token = tokenManager::generateUniqueToken();


	$OauthRequest = OAuth2\Request::createFromGlobals();
	$OauthResponse = new OAuth2\Response();

	// validate the authorize request
	if (!$server->validateAuthorizeRequest($OauthRequest, $OauthResponse)) {
	    $OauthResponse->send();
    	$parameters = $OauthResponse->getParameters();
    	echo "<error><name>".$parameters['error']."</name><message>".$parameters['error_description']."</message></error>";
	    die;
	}

	$clientID = $request->getParam('client_id');
	$page_title = "Authorize";
	$content_name = "authorize";

	return $this->view->render($response, "template.html", array('session' => $_SESSION,
	 'page_title' => $page_title, 'content_name' => $content_name,
	 'clientID' => $clientID, "token" => $token));


});

// Valid user authorization
$app->post('/Oauth/Authorize', function (Request $request, Response $response, $args) use ($server){

	SessionManager::sessionStart('user');


	// Authenticates the user on the website
	if(!$_SESSION['user'])
	{
	    $res = UserModel::checkUser($request->getParam('name'), $request->getParam('password'));
	    if ($res != -1)
	    {
	    	SessionManager::sessionDelete();
	    	$user = UserModel::findUserById($res);
	    	SessionManager::sessionStart('user');
	    	$_SESSION['user'] = $user;
	    }
	    else
	    {
	    		$clientID = $request->getParam('client_id');
				$page_title = "Authorize";
				$content_name = "authorize";
	    	return $this->view->render($response, "template.html", array('session' => $_SESSION,
		 	'page_title' => $page_title, 'content_name' => $content_name,
		 	'clientID' => $clientID, "res" => $res));
	    }
	}

    //Make Oauth accept
	$OauthRequest = OAuth2\Request::createFromGlobals();
	$OauthResponse = new OAuth2\Response();

	$userid = $_SESSION['user']->idUser;

	// print the authorization code if the user has authorized your client
	$is_authorized = ($_POST['authorized'] === 'yes');
	$server->handleAuthorizeRequest($OauthRequest, $OauthResponse, $is_authorized, $userid);

	if ($is_authorized) {
	  // this is only here so that you get to see your code in the cURL request. Otherwise, we'd redirect back to the client
	  $code = substr($OauthResponse->getHttpHeader('Location'), strpos($OauthResponse->getHttpHeader('Location'), 'code=')+5, 40);
	  //exit("SUCCESS! Authorization Code: $code");

	   $params = array(
    'state' => $request->getParam('state'),
    'code' => $code
  	);
	  header('Location: ' . $request->getParam('redirect_uri') . '?' . http_build_query($params));
 	  die();
	}

	$parameters = $OauthResponse->getParameters();
    echo "<error><name>".$parameters['error']."</name><message>".$parameters['error_description']."</message></error>";


});



// Echange code for access token
$app->post('/Oauth/Token', function (Request $request, Response $response, $args) use ($server){

// Handle a request for an OAuth2.0 Access Token and send the response to the client
$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();

});

