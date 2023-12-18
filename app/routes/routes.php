<?php

use App\Config\Middleware\TokenMiddleware;
use App\Controllers\SystemController;
use App\Controllers\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    $app->get('/dev', SystemController::class . ':dev');

    $app->get('/', function (Request $request, Response $response, $args) {
        $response->getBody()->write("Hello world!");
        return $response;
    });

    $app->get('/login', function ($request, $response) {
        $response->getBody()->write("This is a protected route");
        return $response;
    })->add(new TokenMiddleware());


    $app->post('/user', UserController::class . ':registerUser');

    $app->post('/table', SystemController::class . ':addSQLTableIfNotExist');
};
