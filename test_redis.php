<?php

require 'vendor/autoload.php'; // Assurez-vous que l'autoload de Composer est chargé

// Connexion à Redis
$client = new Predis\Client(getenv('REDIS_URL')); // Utiliser REDIS_URL depuis les variables d'environnement

try {
    // Tester une opération simple
    $client->set('test_key', 'Hello Redis');
    $value = $client->get('test_key');
    
    echo "La valeur de 'test_key' est : $value\n";

    // Supprimer la clé après le test
    $client->del('test_key');
} catch (Exception $e) {
    echo "Erreur de connexion à Redis : " . $e->getMessage() . "\n";
}
