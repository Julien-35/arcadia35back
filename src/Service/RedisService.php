<?php

namespace App\Service;

use Predis\Client;

class RedisService
{
    private Client $redisClient;

    public function __construct(Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function incrementImageClicks(string $imageName): void
    {
        $this->redisClient->incr('image_clicks:' . $imageName);
    }

    public function getImageClicks(string $imageName): int
    {
        return (int) $this->redisClient->get('image_clicks:' . $imageName);
    }
}