<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\WebpackEncoreBundle\WebpackEncoreBundle; // Ajoute cette ligne
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    // Ajoute la méthode registerBundles
    protected function registerBundles(): iterable
    {
        $bundles = [
            new FrameworkBundle(),
            new WebpackEncoreBundle(), // Enregistre le bundle Webpack Encore ici
            // Ajoute d'autres bundles nécessaires
        ];

        // Ajoute les bundles pour l'environnement de développement
        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            // Ajoute d'autres bundles pour dev/test si nécessaire
        }

        return $bundles;
    }
}
