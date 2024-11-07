<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Dotenv\Dotenv; // Importation de Dotenv pour charger les variables
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Nelmio\CorsBundle\NelmioCorsBundle; 
use Symfony\Bundle\SecurityBundle\SecurityBundle; 
use Symfony\Bundle\TwigBundle\TwigBundle;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        $bundles = [
            new FrameworkBundle(),
            new WebpackEncoreBundle(),
            new DoctrineBundle(),
            new DoctrineMigrationsBundle(),
            new NelmioCorsBundle(),
            new SecurityBundle(), 
            new TwigBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            // Ajoutez d'autres bundles si nécessaire pour l'environnement dev/test
        }

        return $bundles;
    }

    protected function initializeContainer()
    {
        // Charger les variables d'environnement uniquement en dev/test, pas en prod
        if ($this->getEnvironment() !== 'prod') {
            (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
        }

        parent::initializeContainer();
    }
}
