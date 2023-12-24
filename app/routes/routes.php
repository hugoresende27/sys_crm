<?php

use App\Config\Middleware\TokenMiddleware;
use App\Controllers\MovieController;
use App\Controllers\SystemController;
use App\Controllers\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

$tokenMiddleware = new TokenMiddleware();

return function (App $app) use ($tokenMiddleware) {

    $app->get('/dev', SystemController::class . ':dev');

    $app->get('/', function (Request $request, Response $response, $args) {
        $response->getBody()->write("Hello System CRM");
        return $response;
    });

    $app->group('/api/v1', function (RouteCollectorProxy $routes) use ($tokenMiddleware) {
        $routes->post('/login', UserController::class . ':loginUser');
        $routes->post('/register-dev', UserController::class . ':registerUser');
        
        $routes->group('/user', function (RouteCollectorProxy $group) {
            $group->post('/register', UserController::class . ':registerUser');
            $group->put('/{id}', UserController::class . ':editUser');
            $group->delete('/{id}', UserController::class . ':deleteUser');
            $group->get('/users', UserController::class . ':getUsers');
        })->add($tokenMiddleware);

        $routes->group('/movie', function (RouteCollectorProxy $group) {
            $group->get('/trendings', MovieController::class . ':getTrendings');
            $group->get('/popularity', MovieController::class . ':getPopularity');
        })->add($tokenMiddleware);

        $routes->post('/table', SystemController::class . ':addSQLTableIfNotExist');
    });

};
