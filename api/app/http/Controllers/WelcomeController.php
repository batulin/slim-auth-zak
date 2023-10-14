<?php

namespace App\http\Controllers;

use App\Service\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class WelcomeController
{
    public function __construct(private AuthService $service)
    {
    }

    public function index(Request $request, Response $response)
    {
        $response->getBody()->write("oops");

        return $response;
    }
    public function show(Request $request, Response $response, $name)
    {
        $response->getBody()->write("hello {$name}");

        return $response;
    }

    public function test(Request $request, Response $response)
    {
        $response->getBody()->write(json_encode($this->service->createRefresh(1)));
        return $response;
    }
}