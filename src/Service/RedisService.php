<?php

namespace App\Service;

use Redis;

class RedisService
{
    private $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function getRedis(): Redis
    {
        return $this->redis;
    }

    public function getVisits($animalId)
    {
        return $this->redis->get("animal:{$animalId}:visits");
    }

    // Vous pouvez ajouter d'autres méthodes pour interagir avec Redis
}