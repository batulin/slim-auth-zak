<?php

namespace App\http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class WelcomeController
{
    public function index(Request $request, Response $response, $fff = null)
    {
        $response->getBody()->write("oops");

        return $response;
    }
    public function show(Request $request, Response $response, $name)
    {
        $response->getBody()->write("hello {$name}");

        return $response;
    }
}