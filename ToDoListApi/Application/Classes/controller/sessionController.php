<?php 

//Manage login / logout / sign in pages

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Model\UserModel;
use Helper\SessionManager;
use Helper\tokenManager;


// login Get
$app->get('/login', function (Request $request, Response $response, $args){

    SessionManager::sessionStart('user');


	if($_SESSION['user']->security_level > 0 )
		return $response->withRedirect('/');

	$page_title = "Connection";
	$content_name = 'login';

     return $this->view->render($response, "template.html", array('session' => $_SESSION, 'page_title' => $page_title, 'content_name' => $content_name));
});

// login post
$app->post('/login', function(Request $request, Response $response, $args) use($app){

    SessionManager::sessionStart('user');


	if($_SESSION['user']->security_level > 0 )
		return $response->withRedirect('/');

    $name = $request->getParam('name');
    $password = $request->getParam('password'); // by key


    $res = UserModel::checkUser($name, $password);

    if ($res != -1)
    {
    	SessionManager::sessionDelete();
    	$user = UserModel::findUserById($res);
    	SessionManager::sessionStart('user');
    	$_SESSION['user'] = $user;

    	return $response->withRedirect('/');

    }
    else
    {
    	$content_name = 'login';
    	return $this->view->render($response, "template.html", array('session' => $_SESSION, 'page_title' => $page_title, 'content_name' => $content_name, 'res' => $res));
    }

});

// register Get
$app->get('/register', function (Request $request, Response $response, $args){

    SessionManager::sessionStart('user');


	if($_SESSION['user']->security_level > 0 )
		return $response->withRedirect('/');

	$page_title = "registration";
	$content_name = 'register';
     return $this->view->render($response, "template.html", array('session' => $_SESSION, 'page_title' => $page_title, 'content_name' => $content_name));
});

// login post
$app->post('/register', function(Request $request, Response $response, $args) use($app){

    SessionManager::sessionStart('user');

	if($_SESSION['user']->security_level > 0 )
		return $response->withRedirect('/');


    $name = $request->getParam('name');
    $firstName = $request->getParam('firstName');
    $lastName = $request->getParam('lastName');
    $password = $request->getParam('password'); // by key
    $confirmation = $request->getParam('confirmation');
    $mail = $request->getParam('mail');

    if ($password == $confirmation) {

    	$res =  UserModel::insertUser($name, $password, $firstName, $lastName, $mail, 1);

    	
    	if ($res > 0)
    	{

    	SessionManager::sessionDelete();
    	$id = UserModel::checkUser($name, $password);
    	$user = UserModel::findUserById($id);
    	SessionManager::sessionStart('user');
    	$_SESSION['user'] = $user;

    	return $response->withRedirect('/');

    	}
    	else
    	{

    	$content_name = 'register';
    	return $this->view->render($response, "template.html", array('session' => $_SESSION, 'page_title' => $page_title, 'content_name' => $content_name, 'res' => $res));
    	}
    }
    else
    {
    	$res = -1;
    	$content_name = 'register';
    	return $this->view->render($response, "template.html", array('session' => $_SESSION, 'page_title' => $page_title, 'content_name' => $content_name, 'res' => $res));
    }

});

$app->get('/disconnect', function (Request $request, Response $response, $args){

    SessionManager::sessionStart('user');

    if (!tokenManager::checkToken($request->getQueryParams()['token']))  
        return $response->withRedirect('/');

    if($_SESSION['user']->security_level < 1 || !isset($_SESSION['user']))
        return $response->withRedirect('/login');

    SessionManager::sessionDelete();

    return $response->withRedirect('/');

});
