<?php

namespace App\http\Controllers;

use App\Models\Db;
use DateInterval;
use DateTimeImmutable;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $email = $data['email'];
        $password = $data['password'];

        $sql = "SELECT * FROM user WHERE email = ?";
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $email, PDO::PARAM_STR); // Привязка значения к плейсхолдеру
        $stmt->execute(); // Выполнение запроса
        $user = $stmt->fetch(PDO::FETCH_OBJ); // Извлечение результата запроса
        $db = null;

        if (!$user) {

            $response->getBody()->write(json_encode("user not found" ));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }

        if ($user->password != $password) {
            $response->getBody()->write(json_encode("password is not correct" ));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }

        ////// Создаем токен доступа и токен обновления
        $id = $user->id;
        $iat = (new DateTimeImmutable("now"))->modify('+5 minutes')->getTimestamp();
        $nbf = (new DateTimeImmutable("now"))->getTimestamp();

        $key = 'example_key';
        $payload = [
            'roles' => ['ROLE_USER'],
            'id' => $id,
            'iat' => $iat,
            'nbf' => $nbf
        ];

        $result = $jwt = JWT::encode($payload, $key, 'HS256');

        $response->getBody()->write(json_encode($result));
//
//        var_dump($jwt);
//        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
//        print_r($decoded);

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    }

}