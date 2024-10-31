<?php

namespace App\Service;

use Redis;

class RedisService
{
    private Redis $redis;

    public function __construct(string $redisUrl)
    {
        $this->redis = new Redis();

        // Connexion à Redis en utilisant l'URL fournie
        $urlParts = parse_url($redisUrl);
        $host = $urlParts['host'];
        $port = $urlParts['port'];
        $password = $urlParts['pass'] ?? null;

        // Établir la connexion
        if ($password) {
            $this->redis->connect($host, $port);
            $this->redis->auth($password);
        } else {
            $this->redis->connect($host, $port);
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
