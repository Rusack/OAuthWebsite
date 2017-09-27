<?php

$username = "YourUsername";
$password = "YourPassword";
$dsn = "mysql:host=127.0.0.1;dbname=Authentication_server";

//http://bshaffer.github.io/oauth2-server-php-docs/
require_once('../vendor/bshaffer/oauth2-server-php/src/OAuth2/Autoloader.php');
OAuth2\Autoloader::register();


$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));
$server = new OAuth2\Server($storage);


// Add the "Client Credentials" grant type (it is the simplest of the grant types)
//server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage)); // or any grant type you like!
// Add the "Authorization Code" grant type (this is where the oauth magic happens)
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));

