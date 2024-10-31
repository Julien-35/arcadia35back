<?php
// test_redis.php

// Récupérer l'URL de connexion à Redis depuis les variables d'environnement
$redisUrl = getenv('OPENREDIS_URL');

// Créer une instance de Redis
$redis = new Redis();

try {
    // Se connecter à Redis
    $redis->connect(parse_url($redisUrl, PHP_URL_HOST), parse_url($redisUrl, PHP_URL_PORT));
    $redis->auth(parse_url($redisUrl, PHP_URL_USER), parse_url($redisUrl, PHP_URL_PASS));

    echo "Connection to Redis is working.";
} catch (Exception $e) {
    echo "Failed to connect to Redis: " . $e->getMessage();
}
