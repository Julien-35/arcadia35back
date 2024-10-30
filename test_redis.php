<?php

require 'vendor/autoload.php'; // Chargez l'autoload de Composer

$client = new Predis\Client([
    'scheme' => 'tcp',
    'host' => 'ec2-54-220-89-179.eu-west-1.compute.amazonaws.com', // Remplacez par votre hôte Redis
    'port' => 32380,
    'password' => 'p413454e4afee40d5654397dcd27d7b0e753b5adf13b4cc901445bd130a5059c5', // Remplacez par votre mot de passe
]);

try {
    $client->connect();
    echo "Connexion à Redis réussie!";
} catch (Exception $e) {
    echo "Erreur de connexion à Redis: " . $e->getMessage();
}
