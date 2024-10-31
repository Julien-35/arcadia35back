<?php

namespace App\Service;

use Redis;
use Symfony\Component\HttpFoundation\JsonResponse;

class RedisService {
    private Redis $redis;

    public function __construct(Redis $redis) {
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

    public function testRedis(): JsonResponse
    {
        try {
            $visits = $this->getVisits(1); // Utilisez un ID arbitraire
            return new JsonResponse(['success' => 'Redis connection is working', 'visits' => $visits]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Redis connection failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}