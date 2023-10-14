<?php

namespace App\Dto;

class TokenResponse
{
    private string $refresh_token;
    private string $access_token;

    public function __construct(string $refresh_token, string $access_token)
    {
        $this->refresh_token = $refresh_token;
        $this->access_token = $access_token;
    }

    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

}