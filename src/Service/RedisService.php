<?php

namespace App\Service;

use Redis;

class RedisService
{
    private Redis $redis;

    public function __construct() // Pas besoin de passer Redis ici
    {
        $this->redis = new Redis();
        $this->redis->connect(parse_url(getenv('REDIS_URL'), PHP_URL_HOST), parse_url(getenv('REDIS_URL'), PHP_URL_PORT));
        $this->redis->auth(parse_url(getenv('REDIS_URL'), PHP_URL_USER));
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
