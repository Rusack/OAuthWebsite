<?php 

//Manage login / logout / sign in pages
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Model\UserModel;
use Helper\SessionManager;
use Helper\tokenManager;


$app->get('/404', function  (Request $request, Response $response, $args) use ($app, $obj, $router) {

	return "Page not found.";
});


$app->get('/', function  (Request $request, Response $response, $args) use ($app, $obj, $router) {

    SessionManager::sessionStart('user');

    $token = tokenManager::generateUniqueToken();

    if($_SESSION['user']->security_level < 1 || !isset($_SESSION['user'] ))
    	return $response->withRedirect('/login');    
    $page_title = "Api Calls";
    $content_name = "apiCalls";

    $apiCallList = array(
    	array('name' => "User info",  'address' => "/api/user",'description' => "Get informations of current user"),
    	array('name' => "To Do list",  'address' => "/api/ToDoList",'description' => "Get all to do lists of the current user"),
    	array('name' => "Add To Do list",  'address' => "/api/ToDoList/add",'description' => "Add a new To Do list to the list of the current user"),
    	array('name' => "Delete a To Do list",  'address' => "/api/ToDoList/del/{listID}",'description' => "Delete a to do list of the current user"),
    	array('name' => "Task list",  'address' => "/api/ToDoList/detail/{listID}",'description' => "Get the task list of a to do list"),
    	array('name' => "Add task",  'address' => "/api/ToDoList/detail/{listID}/add",'description' => "Add a new task to the to do list"),
    	array('name' => "Delete task",  'address' => "/api/ToDoList/detail/{listID}/del/{taskID}",'description' => "Delete a task of the to do list")
    	);

	return $this->view->render($response, "template.html", array('session' => $_SESSION, 'page_title' => $page_title, 'content_name' => $content_name, 'apiCallList' => $apiCallList, "token" =>$token ));


});