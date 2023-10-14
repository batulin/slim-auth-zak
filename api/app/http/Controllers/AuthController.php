<?php

namespace App\http\Controllers;

use App\Models\Db;
use DateTimeImmutable;
use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;

class AuthController
{
    public function signup(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $email = $data['email'];
        $password = $data['password'];

        if (empty($email) || empty($password)) {
            $response->getBody()->write(json_encode("заполните все поля" ));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->getBody()->write(json_encode("заполните поле email корректно" ));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->getBody()->write(json_encode("заполните поле email корректно" ));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
        if (strlen($password) < 8) {
            $response->getBody()->write(json_encode("заполните поле password корректно" ));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }

        $sql = "SELECT * FROM user WHERE email = ?";
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $email, PDO::PARAM_STR); // Привязка значения к плейсхолдеру
        $stmt->execute(); // Выполнение запроса
        $user = $stmt->fetch(PDO::FETCH_OBJ); // Извлечение результата запроса
        $db = null;

        if ($user) {
            $response->getBody()->write(json_encode("user already exists" ));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(409);
        }

        $sql = "INSERT INTO user (email, password) VALUES (:email, :password)";

        try {
            $db = new Db();
            $conn = $db->connect();

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);

            $result = $stmt->execute();

            $db = null;
            $response->getBody()->write(json_encode($result));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } catch (PDOException $e) {
            $error = array(
                "message" => $e->getMessage()
            );

            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
    }

    public function login(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        var_dump($data);
        die();
        $email = $data['email'];
        $password = $data['password'];

        $sql = "SELECT * FROM user WHERE email = ?";
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);
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


// end create token
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