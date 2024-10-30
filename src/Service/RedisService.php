<?php

namespace App\Service;

use Redis;

class RedisService
{
    private Redis $redis;

    public function __construct(Redis $redis) 
    {
        $this->redis = $redis;
    }

    public function incrementVisits(int $animalId): void
    {
        $this->redis->incr('animal_visits:' . $animalId);
    }

    public function getVisits(int $animalId): int
    {
        return (int)$this->redis->get('animal_visits:' . $animalId);
    }
}