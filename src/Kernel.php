<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Nelmio\CorsBundle\NelmioCorsBundle; 
use Symfony\Bundle\SecurityBundle\SecurityBundle; // Assurez-vous d'importer le SecurityBundle
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
            // Ajoutez d'autres bundles si nécessaire
        }

        return $bundles;
    }

    protected function initializeContainer()
    {
        if ($this->getEnvironment() !== 'prod') {
            (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
        }

        parent::initializeContainer();
    }
}
