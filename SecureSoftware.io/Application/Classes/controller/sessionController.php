<?php 
 
//Manage login / logout / sign in pages

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Helper\SessionManager;
use Helper\ApiCaller;
use Helper\tokenManager;
use Helper\RefreshToken;



$app->get('/', function  (Request $request, Response $response, $args) use ($app, $obj, $router) {

    SessionManager::sessionStart('user');

    if(isset($_SESSION['refresh_token']))
        RefreshToken::checkValidity();


    if($_SESSION['user']->security_level > 0 )
        return $response->withRedirect('/list');

    $page_title = "Home";
    $content_name = "home";

    $token = tokenManager::generateUniqueToken();

    return $this->view->render($response, "template.html", array('session' => $_SESSION, 'page_title' => $page_title, 'content_name' => $content_name, 
        'token' => $token));
});


$app->get('/code', function  (Request $request, Response $response, $args) use ($app, $obj, $router) {

    SessionManager::sessionStart('user');

    $page_title = "code";

    $code = $request->getParam('code');

    if($code) {
      // Verify the state matches our stored state
      if(!tokenManager::checkToken($request->getParam('state'))) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=invalid_state');
        die();
      }
 

      $tokenURL = "http://todolistapi.io/Oauth/Token";
      // Exchange the auth code for a token
      $token = json_decode(apiCaller::apiRequest($tokenURL, array(
        'client_id' => 'todolist',  
        'client_secret' => 'todolist',
        'redirect_uri' => 'http://securesoftware.io/code',
        'state' => $_SESSION['state'],
        'code' => $code,
        'grant_type' => "authorization_code"
      )));
      $_SESSION['access_token'] = $token->access_token;
      $_SESSION['expires'] = $token->expires;
      $_SESSION['refresh_token'] =  $token->refresh_token;
      $url = "http://todolistapi.io/api/user";
      
      $user = json_decode(apiCaller::apiRequest($url,array()), true);
    
      $_SESSION['user'] = $user;
          
      return $response->withRedirect('/todolist');    

    }
});

