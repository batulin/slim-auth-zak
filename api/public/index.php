<?php
use DI\Container;
use Slim\Factory\AppFactory;
use App\Models\User;

require __DIR__ . '/../vendor/autoload.php';

$user = new User;
die();

$container = new Container();

$settings = require __DIR__ . '/../app/settings.php';
$settings($container);

AppFactory::setContainer($container);

$app = AppFactory::create();

$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);

$routes = require __DIR__ . '/../app/routes.php';
$routes($app);



$app->run();