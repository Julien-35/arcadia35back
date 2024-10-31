<?php

namespace App\Factory;

use Predis\Client; // ou Redis si vous utilisez l'extension Redis

class RedisFactory
{
    private string $redisUrl;

    public function __construct(string $redisUrl)
    {
        $this->redisUrl = $redisUrl;
    }

    public function create(): Client
    {
        return new Client($this->redisUrl);
    }
}
