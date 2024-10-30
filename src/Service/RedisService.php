<?php

namespace App\Service;

use Redis;

class RedisService
{
    private Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;

        // Connexion à Redis
        try {
            $this->redis->connect(parse_url(getenv('REDIS_URL'))['host'], parse_url(getenv('REDIS_URL'))['port']);
            if (isset(parse_url(getenv('REDIS_URL'))['pass'])) {
                $this->redis->auth(parse_url(getenv('REDIS_URL'))['pass']);
            }
        } catch (\Exception $e) {
            // Gérer l'erreur de connexion
            throw new \RuntimeException('Erreur de connexion à Redis : ' . $e->getMessage());
        }
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
