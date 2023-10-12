<?php

namespace App\middleware;

use DateTimeImmutable;
use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ExampleBeforeMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = new Response();
        try {
            if ($request->hasHeader("Authorization")) {
                $header = $request->getHeader("Authorization");

                if (!empty($header)) {
                    $bearer = trim($header[0]);
                    preg_match("/Bearer\s(\S+)/", $bearer, $matches);
                    $token = $matches[1];

                    $key = new Key("example_key", "HS256");
                    try {
                        $dataToken = JWT::decode($token, $key);
                    } catch (Exception $e) {
                        var_dump($e);
                    }
                    $now = (new DateTimeImmutable("now"))->getTimestamp();

                    if ($dataToken->iat < $now) {
                        $response->getBody()->write(
                            json_encode([
                                "Error" => [
                                    "Message" => "token expires"
                                ]])
                        );
                        return $response
                            ->withHeader("Content-Type", "application/json")
                            ->withStatus(401);
                    }
                }
            } else {
                $response->getBody()->write(json_encode([
                    "Error" => [
                        "Message" => "unauthorized"
                    ]
                ]));
                return $response
                    ->withHeader("Content-Type", "application/json")
                    ->withStatus(401);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                "Error" => [
                    "Message" => $e->getMessage()
                ]
            ]));
            return $response
                ->withHeader("Content-Type", "application/json")
                ->withStatus(500);
        }
        $response = $handler->handle($request);
        return $response;
    }
}
