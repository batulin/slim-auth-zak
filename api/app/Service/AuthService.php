<?php

namespace App\Service;

use App\Exception\UnauthorizedException;
use App\Exception\UncorrectPasswordException;
use App\Exception\UserNotFoundException;
use App\Exception\ValidationException;
use App\Models\Db;
use DateTimeImmutable;
use Firebase\JWT\JWT;
use PDO;
use PDOException;
use Ramsey\Uuid\Nonstandard\Uuid;

class AuthService
{
    public function __construct(private Db $db)
    {
    }

    public function login(array $data)
    {
        $email = $data['username'];
        $password = $data['password'];
        if (empty($email) || empty($password)) {
            throw new ValidationException();
        }

        $sql = "SELECT * FROM user WHERE email = ?";
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if (!$user) {
            throw new UserNotFoundException();
        }

        if ($user->password != $password) {
            throw new UncorrectPasswordException();
        }

        ////// Создаем токен доступа и токен обновления
        $accessToken = $this->createAccess($user->id);
        $refreshToken = $this->createRefresh($user->id);
        ///// end create token

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ];
    }

    public function refresh(string $oldRefreshToken): array
    {
        $oldRefreshSession = $this->getSessionByToken($oldRefreshToken);
        if (!$oldRefreshSession) {
            throw new UnauthorizedException();
        }
        $now = (new DateTimeImmutable())->format('Y-m-d H:i:s');

        if ($oldRefreshSession->expired < $now) {
            throw new UnauthorizedException();
        }

        // create
        $userId = $oldRefreshSession->user_id;
        $refreshToken = $this->createRefresh($userId);
        $accessToken = $this->createAccess($userId);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ];
    }

    public function createRefresh(int $user_id)
    {
        $uuid = Uuid::uuid4();
        $expired = (new DateTimeImmutable("now"))->modify('+5 minutes')->format('Y-m-d H:i:s');
        $createdAt = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        $sql = "INSERT INTO refresh_session (user_id, refresh_token, created_at, expired) VALUES (:user_id, :refresh_token, :created_at, :expired)";
        try {
            $db = new Db();
            $conn = $db->connect();

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':refresh_token', $uuid);
            $stmt->bindParam(':created_at', $createdAt);
            $stmt->bindParam(':expired', $expired);

            $result = $stmt->execute();

            $db = null;

            return $uuid;

        } catch (PDOException $e) {
            $error = array(
                "message" => $e->getMessage()
            );
            return $error;
        }
    }

    public function getSessionByToken(string $token)
    {
        $sql = "SELECT * FROM refresh_session WHERE refresh_token = ?";
        $db = new Db();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $token, PDO::PARAM_STR);
        $stmt->execute();
        $refreshSession = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        return $refreshSession;
    }

    public function createAccess($userId)
    {
        $iat = (new DateTimeImmutable("now"))->modify('+5 minutes')->getTimestamp();
        $nbf = (new DateTimeImmutable("now"))->getTimestamp();

        $key = 'example_key';
        $payload = [
            'roles' => ['ROLE_USER'],
            'id' => $userId,
            'iat' => $iat,
            'nbf' => $nbf
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

}