<?php
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$settings = require __DIR__ . '/../app/settings.php';
$settings($container);

AppFactory::setContainer($container);

$app = AppFactory::create();

$middleware = require __DIR__ . '/../app/middleware.php';
$middleware($app);


$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->run();