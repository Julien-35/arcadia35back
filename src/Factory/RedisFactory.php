<?php

namespace App\Factory;

use Redis;

class RedisFactory {
    private string $redisUrl;

    public function __construct(string $redisUrl) {
        $this->redisUrl = $redisUrl;
    }

    public function create(): Redis {
        $redis = new Redis();
        $redis->connect($this->redisUrl);
        return $redis;
    }
}