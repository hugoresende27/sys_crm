<?php

use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers.php';

$container = new Container;

$settings = require __DIR__ . '/../app/settings.php';
$settings($container);

AppFactory::setContainer($container);
$app = AppFactory::create();


$app = AppFactory::create();

$middleware = require __DIR__ . '/../app/middleware/middleware.php';
$middleware($app);

$app->addErrorMiddleware(true,true,true);


//ROUTES -----------------------------
$app->get('/dev', function (Request $request, Response $response, $args) {
    if (extension_loaded('sodium')) {
        echo 'Libsodium is installed.';
    } else {
        echo 'Libsodium is not installed.';
    };
    die();
    $response->getBody()->write(json_encode($r ?? ""));
    return $response;
});
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});


require __DIR__ . '/../app/middleware/TokenMiddleware.php';
$app->get('/login', function ($request, $response) {
    $response->getBody()->write("This is a protected route");
    return $response;
})->add(new TokenMiddleware());


$app->run();