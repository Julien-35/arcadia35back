<?php
// test_redis.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Redis;

class RedisTestController extends AbstractController
{
    #[Route('/test-redis', name: 'test_redis')]
    public function testRedis(): JsonResponse
    {
        $redisUrl = getenv('OPENREDIS_URL'); // Récupérer l'URL de Redis
        $redis = new Redis();

        try {
            // Se connecter à Redis
            $redis->connect(parse_url($redisUrl, PHP_URL_HOST), parse_url($redisUrl, PHP_URL_PORT));

            // Vérifiez si un mot de passe est présent et authentifiez si c'est le cas
            $password = parse_url($redisUrl, PHP_URL_PASS);
            if ($password) {
                $redis->auth($password);
            }

            return new JsonResponse(['message' => 'Connection to Redis is working.']);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Failed to connect to Redis: ' . $e->getMessage()], 500);
        }
    }
}