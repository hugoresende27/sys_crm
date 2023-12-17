<?php

use App\Middleware\TokenMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    $app->get('/dev', function (Request $request, Response $response, $args) {

            // Get PDO instance from the container
        $pdo = $this->get(PDO::class);
        // dd('pdo',$pdo);
        // Example query
        $stmt = $pdo->prepare('SELECT * FROM users');
        $stmt->execute();
        $data = $stmt->fetchAll();
        dd($data);

        dd($_ENV['APP_LOCAL']);
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

    $app->get('/login', function ($request, $response) {
        $response->getBody()->write("This is a protected route");
        return $response;
    })->add(new TokenMiddleware());
};
