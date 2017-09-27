<?php 
// manage secured user part of the website

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Helper\SessionManager;
use Helper\tokenManager;
use Helper\ApiCaller;
use Helper\RefreshToken;


$apiPath = "http://todolistapi.io/api/";
$res = 0;

// login Get
$app->get('/todolist', function (Request $request, Response $response, $args) use ($apiPath, $res){


	SessionManager::sessionStart('user');


	if(!isset($_SESSION['user']))
    	return $response->withRedirect('/');

	$page_title = "ToDoList";
	$content_name = 'todolist';

    $token = tokenManager::generateUniqueToken();

    $url = $apiPath."ToDoList";

	$list = json_decode(apiCaller::apiRequest($url, array()),true);
     
     
     return $this->view->render($response, "template.html", array('session' => $_SESSION, 'page_title' => $page_title, 'content_name' => $content_name, 'list' => $list, 'token' => $token, 'res' => $res));
        
});

$app->post('/todolist', function (Request $request, Response $response, $args) use ($apiPath, $res){

    SessionManager::sessionStart('user');


    if (!tokenManager::checkToken($request->getParam('token')))  
        return $response->withRedirect('/todolist');

	if(!isset($_SESSION['user']))
    	return $response->withRedirect('/');
    
    $name = $request->getParam('name');

    $url = $apiPath."ToDoList/add";

    $res = apiCaller::apiRequest($url, array('name' => $name ), array());

	return $response->withRedirect('/todolist');

});


$app->get('/home/del/{listID}', function (Request $request, Response $response, $args) use ($apiPath){

    SessionManager::sessionStart('user');

	if(!isset($_SESSION['user']))
    	return $response->withRedirect('/');


    if (!tokenManager::checkToken($request->getQueryParams()['token']))  
        return $response->withRedirect('/home');

    $listID = $request->getAttribute('listID');

    $url = $apiPath."ToDoList/del/".$listID;


    $res = apiCaller::apiRequest($url, array());
    
    if ($res > 0) {
    	return $response->withRedirect('/todolist');
    }
    else
    {

    return $response->withRedirect('/todolist');

    }


});

$app->get('/detail/{listID}', function (Request $request, Response $response, $args) use ($apiPath){

    SessionManager::sessionStart('user');


	if(!isset($_SESSION['user']))
    	return $response->withRedirect('/');

    $listID = $request->getAttribute('listID');

    $url = $apiPath."ToDoList/detail/".$listID;

    $taskList = json_decode(apiCaller::apiRequest($url, array()), true);

	$page_title = "ToDoList";
	$content_name = 'listDetail';

    $token = tokenManager::generateUniqueToken();


     return $this->view->render($response, "template.html", array('session' => $_SESSION, 'page_title' => $page_title, 'content_name' => $content_name,
        'taskList' => $taskList, 'listID' => $listID, 'token' => $token));
        
});

$app->post('/detail/{listID}', function (Request $request, Response $response, $args) use ($apiPath){

    SessionManager::sessionStart('user');


    $listID = $request->getAttribute('listID');


    if (!tokenManager::checkToken($request->getParam('token')))  
        return $response->withRedirect('/detail/'.$listID);

	if(!isset($_SESSION['user']))
    	return $response->withRedirect('/');

    $deadline = $request->getParam('deadline');
    $description = $request->getParam('description');

    $url = $apiPath."ToDoList/detail/".$listID."/add";


    $res = json_decode(
        apiCaller::apiRequest($url, array('deadline' => $deadline, 'description' => $description), array())
        , true);

    print_r($res);
	if ($res > 0)
	{

	return $response->withRedirect('/detail/'.$listID);

	}
	else
	{

    return $response->withRedirect('/detail/'.$listID);

	}

});

$app->get('/detail/{listID}/del/{taskID}', function (Request $request, Response $response, $args) use ($apiPath){

    SessionManager::sessionStart('user');


    $listID = $request->getAttribute('listID');


    if (!tokenManager::checkToken($request->getQueryParams()['token']))  
        return $response->withRedirect('/detail/'.$listID);

	if(!isset($_SESSION['user']))
    	return $response->withRedirect('/');

    $taskID = $request->getAttribute('taskID');

    $url = $apiPath."ToDoList/detail/".$listID."/del/".$taskID;


    $res = json_decode(
        apiCaller::apiRequest($url, array())
        , true);


    if ($res > 0) {
	return $response->withRedirect('/detail/'.$listID);
    }
    else
    {
	return $response->withRedirect('/detail/'.$listID);
    }


});

// login Get
$app->get('/disconnect', function (Request $request, Response $response, $args) use ($apiPath){

    SessionManager::sessionStart('user');

    if (!tokenManager::checkToken($request->getQueryParams()['token']))  
        return $response->withRedirect('/');

	if(!isset($_SESSION['user']))
    	return $response->withRedirect('/login');

    SessionManager::sessionDelete();

	return $response->withRedirect('/');

});
