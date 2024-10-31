<?php

namespace App\Service;

use Redis;
use Exception;

class RedisService
{
    private Redis $redis;

    public function __construct(string $redisUrl)
    {
        $this->redis = new Redis();

        try {
            $this->redis->connect(parse_url($redisUrl, PHP_URL_HOST), parse_url($redisUrl, PHP_URL_PORT));
            $password = parse_url($redisUrl, PHP_URL_PASS);
            if ($password) {
                $this->redis->auth($password);
            }
        } catch (Exception $e) {
            throw new Exception('Redis connection failed: ' . $e->getMessage());
        }
    }

    public function incrementVisits(int $animalId): void
    {
        $this->redis->incr("animal:{$animalId}:visits");
    }

    public function getVisits(int $animalId): int
    {
        return (int) $this->redis->get("animal:{$animalId}:visits") ?: 0;
    }
}