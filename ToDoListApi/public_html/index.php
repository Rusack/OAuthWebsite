<?php

error_reporting(E_ALL ^ E_NOTICE);

require_once('../vendor/autoload.php');

//require_once('../vendor/bshaffer/boauth2-server-php/src/OAuth2/Autoloader.php');
//OAuth2\Autoloader::register();

use Helper\SessionManager;
use Model\UserModel;
use Model\ToDoListModel;
use Model\TaskModel;

$username = "YourUsername";
$password = "YourPassword";
$dsn = "mysql:host=127.0.0.1;dbname=Authentication_server";

$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
$server = new OAuth2\Server($storage);
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage,  array(
    'always_issue_new_refresh_token' => true,
    'refresh_token_lifetime'         => 2419200)));


$config = [
    'settings' => [
        // Slim Settings
	    'determineRouteBeforeAppMiddleware' => true,
	    'displayErrorDetails' => true,
	    'addContentLengthHeader' => false,
    ],
];

$app = new \Slim\App($config);

$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('view/templates', [
        'cache' => false,
        'auto_reload' => true
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));

    return $view;
};


require '../Application/Classes/controller/sessionController.php';
require '../Application/Classes/controller/apiController.php';
require '../Application/Classes/controller/connectedController.php';
require '../Application/Classes/controller/OauthController.php';




$app->run();
