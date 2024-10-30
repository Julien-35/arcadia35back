<?php

// src/Command/TestRedisCommand.php
namespace App\Command;

use Redis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestRedisCommand extends Command
{
    protected static $defaultName = 'app:test-redis';

    private Redis $redis;

    public function __construct(Redis $redis)
    {
        parent::__construct();
        $this->redis = $redis;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Exemple de test de connexion à Redis
        try {
            $this->redis->set('test_key', 'Hello Redis!');
            $output->writeln('Successfully connected to Redis and set test_key.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Failed to connect to Redis: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
