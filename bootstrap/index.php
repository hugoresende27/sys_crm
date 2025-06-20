<?php

use App\Config\Database\PdoConnection;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Tuupola\Middleware\CorsMiddleware;

require __DIR__ . '/../vendor/autoload.php';


//..DOTENV
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

//DATABASE SQL
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    'pdo' => function () {
        return PdoConnection::getInstance(
            $_ENV['DB_HOST'],
            $_ENV['DB_NAME'],
            $_ENV['DB_PORT'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS']);
    }
]);

$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();


$app->add(new CorsMiddleware([
    'origin' => ['*'],
    'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
    'headers.allow' => ['Content-Type', 'Authorization'],
    'credentials' => true,
]));


//SETTINGS
$settings = require __DIR__ . '/../app/settings.php';
$settings($container);


//MIDDLEWARE
$middleware = require __DIR__ . '/../app/config/middleware/middleware.php';
$middleware($app);
$app->addErrorMiddleware(true,true,true);


//ROUTES
$routes = require __DIR__ . '/../app/routes/routes.php';
$routes($app);


$app->run();