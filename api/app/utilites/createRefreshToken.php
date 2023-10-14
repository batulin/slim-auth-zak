<?php

use Ramsey\Uuid\Nonstandard\Uuid;
use \DateTimeImmutable;

return function (int $user_id) {
    $uuid = Uuid::uuid4();
    $expired = (new DateTimeImmutable("now"))->modify('+5 minutes')->getTimestamp();
    $createdAt = (new DateTimeImmutable("now"))->getTimestamp();
};