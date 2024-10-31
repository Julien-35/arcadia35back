<?php

namespace App\Service;

use Redis;
use Exception;
use Psr\Log\LoggerInterface;

class RedisService
{
    private Redis $redis;
    private LoggerInterface $logger;

    public function __construct(string $redisUrl, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->redis = new Redis();

        try {
            $this->redis->connect(parse_url($redisUrl, PHP_URL_HOST), parse_url($redisUrl, PHP_URL_PORT));
            $password = parse_url($redisUrl, PHP_URL_PASS);
            if ($password) {
                $this->redis->auth($password);
            }
            $this->logger->info('Redis connection established successfully.');
        } catch (Exception $e) {
            $this->logger->error('Redis connection failed: ' . $e->getMessage());
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

    // Ajoutez une méthode pour vérifier la connexion si nécessaire
    public function isConnected(): bool
    {
        try {
            return $this->redis->ping() === '+PONG';
        } catch (Exception $e) {
            return false;
        }
    }

    // Ajoutez une méthode pour fermer la connexion si nécessaire
    public function close(): void
    {
        $this->redis->close();
    }
}
