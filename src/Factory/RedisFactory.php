<?php

namespace App\Factory;

use Predis\Client;

class RedisFactory
{
    private string $redisUrl;

    public function __construct(string $redisUrl)
    {
        $this->redisUrl = $redisUrl;
    }

    public function createRedis(): Client {
        return new Client($this->redisUrl);
    }
}
