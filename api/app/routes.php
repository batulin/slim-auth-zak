<?php

use App\http\Controllers\WelcomeController;
use Slim\App;

return function (App $app) {
    $app->get('/', [WelcomeController::class, 'index']);
    $app->get('/{name}', [WelcomeController::class, 'show']);
};