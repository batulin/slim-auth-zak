<?php

use App\http\Controllers\AuthController;
use App\http\Controllers\RefreshTokenController;
use App\http\Controllers\WelcomeController;
use App\middleware\ExampleBeforeMiddleware;
use Slim\App;


return function (App $app) {
    $app->get('/', [WelcomeController::class, 'index'])->add(new ExampleBeforeMiddleware());
    $app->get('/test', [WelcomeController::class, 'test']);
    $app->get('/{name}', [WelcomeController::class, 'show']);
    $app->post('/login', [AuthController::class, 'login']);
    $app->post('/signup', [AuthController::class, 'signup']);
    $app->post('/token/refresh', [RefreshTokenController::class, 'refresh']);
};