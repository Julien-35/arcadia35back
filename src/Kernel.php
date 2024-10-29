<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\WebpackEncoreBundle\WebpackEncoreBundle; 
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    // Ajoute la méthode registerBundles
    public function registerBundles(): iterable
    {
        $bundles = [
            new FrameworkBundle(),
            new WebpackEncoreBundle(), // Enregistre le bundle Webpack Encore ici
            // Ajoutez d'autres bundles nécessaires
        ];
    
        // Ajoutez les bundles pour l'environnement de développement
        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            // Ajoutez d'autres bundles pour dev/test si nécessaire
        }
    
        return $bundles;
    }    
}
