<?php

error_reporting(E_ALL ^ E_NOTICE);

require_once('../vendor/autoload.php');

use Helper\SessionManager;
use Model\UserModel;
use Model\ToDoListModel;
use Model\TaskModel;

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
require '../Application/Classes/controller/userController.php';


$app->run();