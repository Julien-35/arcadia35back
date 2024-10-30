<?php

namespace App\Service;

use Redis;

class RedisService
{
    private Redis $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(parse_url(getenv('REDIS_URL'), PHP_URL_HOST), parse_url(getenv('REDIS_URL'), PHP_URL_PORT));

        // Vérifiez si l'authentification est nécessaire
        $password = parse_url(getenv('REDIS_URL'), PHP_URL_PASS);
        if ($password) {
            $this->redis->auth($password);
        }
    }

    public function incrementVisits(int $animalId): void
    {
        $this->redis->incr('animal_visits:' . $animalId);
    }

    public function getVisits(int $animalId): int
    {
        return (int) $this->redis->get('animal_visits:' . $animalId);
    }
}
