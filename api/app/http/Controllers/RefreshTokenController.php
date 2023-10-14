<?php

namespace App\http\Controllers;

use App\Exception\UnauthorizedException;
use App\Models\Db;
use App\Service\AuthService;
use DateTimeImmutable;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Nonstandard\Uuid;

class RefreshTokenController
{
    public function __construct(private AuthService $service)
    {
    }

    public function refresh(Request $request, Response $response)
    {
        try {
            $refreshToken = $request->getParsedBody()['refresh_token'];
            $response->getBody()->write(json_encode($this->service->refresh($refreshToken)));
            return $response;
        } catch (UnauthorizedException $e) {
            $response->getBody()->write(json_encode(["message" => $e->getMessage()]));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(401);
        }
    }

}