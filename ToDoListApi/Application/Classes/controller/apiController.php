<?php 
// manage secured user part of the website

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Model\ToDoListModel;
use Model\TaskModel;
use Model\UserModel;
use Helper\SessionManager;
use Helper\tokenManager;



// are used for OAuth, the user id permits to determine the user ressources
// the access token permits to determine if the user has an authorization to access these ressources

// Get user informations
$app->get('/api/user', function (Request $request, Response $response, $args) use ($server){

    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
    }
    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());    

    $user = UserModel::findUserById($token['user_id']);
    return json_encode($user);

});

// Get To do list list
$app->get('/api/ToDoList', function (Request $request, Response $response, $args) use ($server){

    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
    }
    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());

	$list = ToDoListModel::findToDoListByUserId($token['user_id']);
	return json_encode($list);
});

// Add To Do list
$app->post('/api/ToDoList/add', function (Request $request, Response $response, $args) use ($server){

    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
    }
    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());

	$name = $request->getParam('name');

	$res = ToDoListModel::insertToDoList($token['user_id'], $name);

	return json_encode($res);
});

// Delete To Do list
$app->get('/api/ToDoList/del/{listID}', function (Request $request, Response $response, $args) use ($server){

    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
    }

    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());

    $listID = $request->getAttribute('listID');

    $toDoList = ToDoListModel::findToDoListById($listID);

    if ($toDoList->idUser != $token['user_id'])
        return json_encode(array('res' => 0 ));


    $res = ToDoListModel::deleteToDoList($listID);
    
    return json_encode($res);

 });


// Get details of a To Do list list (task list)
$app->get('/api/ToDoList/detail/{listID}', function (Request $request, Response $response, $args) use ($server){

    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
    }
    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());

    $listID = $request->getAttribute('listID');


    $toDoList = ToDoListModel::findToDoListById($listID);

    if ($toDoList->idUser != $token['user_id'])
        return json_encode(array('res' => 0 ));

    $taskList = TaskModel::findTaskByListId($listID);

    return json_encode($taskList);

});

// Add a task to a todo list
$app->post('/api/ToDoList/detail/{listID}/add', function (Request $request, Response $response, $args) use ($server){

    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
    }
    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
  
    $deadline = $request->getParam('deadline');
    $description = $request->getParam('description');
    $listID = $request->getAttribute('listID');

    $toDoList = ToDoListModel::findToDoListById($listID);

    if ($toDoList->idUser != $token['user_id'])
        return json_encode(array('res' => 0 ));


	$res = TaskModel::insertTask($listID, $description, $deadline);

	return json_encode($res);

});

// Delete a task from a to do list
$app->get('/api/ToDoList/detail/{listID}/del/{taskID}', function (Request $request, Response $response, $args) use ($server){

    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
    }
    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());

    $taskID = $request->getAttribute('taskID');
    $listID = $request->getAttribute('listID');

    $toDoList = ToDoListModel::findToDoListById($listID);

    if ($toDoList->idUser != $token['user_id'])
        return json_encode(array('res' => 0 ));

    $res = TaskModel::deleteTask($taskID);
    
    return json_encode($res);
});


// Delete To Do list
$app->get('/api/tokenCheck', function (Request $request, Response $response, $args) use ($server){

    if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
    }

    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());

    return json_encode(time());

 });
