<?php

namespace App\Service;

use Redis;
use Exception;

class RedisService
{
    private Redis $redis;

    public function __construct()
    {
        $redisUrl = getenv('REDISCLOUD_URL');
        if ($redisUrl === false) {
            throw new Exception("REDISCLOUD_URL is not set.");
        }

        $this->redis = new Redis();
        
        // Connexion à Redis en utilisant l'URL de l'environnement
        $urlParts = parse_url($redisUrl);
        
        // Vérifiez les parties de l'URL
        if (isset($urlParts['host'], $urlParts['port'])) {
            try {
                $this->redis->connect($urlParts['host'], $urlParts['port']);
                if (isset($urlParts['user'], $urlParts['pass'])) {
                    $this->redis->auth($urlParts['user'], $urlParts['pass']);
                }
                echo "Connexion à Redis réussie !"; // Ajouté pour confirmation
            } catch (Exception $e) {
                throw new Exception("Erreur de connexion à Redis : " . $e->getMessage());
            }
        } else {
            throw new Exception("Invalid REDISCLOUD_URL format.");
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
