<?php

use App\Database\PdoConnection;
use App\Middleware\TokenMiddleware;
use DI\Container;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';


//..DOTENV
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Set up dependency injection container
$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();
$container->set(PDO::class, fn () => PdoConnection::getInstance(
    $_ENV['DB_HOST'],
    $_ENV['DB_NAME'],
    $_ENV['DB_PORT'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
));
// $container = new Container;
AppFactory::setContainer($container);
$app = AppFactory::create();



//SETTINGS
$settings = require __DIR__ . '/../app/settings.php';
$settings($container);


//MIDDLEWARE
$middleware = require __DIR__ . '/../app/middleware/middleware.php';
$middleware($app);
$app->addErrorMiddleware(true,true,true);


//ROUTES
$routes = require __DIR__ . '/../app/routes/routes.php';
$routes($app);


// 
// $containerBuilder = new ContainerBuilder();
// $container = $containerBuilder->build();

// Register PDO connection in the container



$app->run();