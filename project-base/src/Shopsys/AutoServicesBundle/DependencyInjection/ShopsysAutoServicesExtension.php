<?php

namespace Shopsys\AutoServicesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ShopsysAutoServicesExtension extends Extension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $containerBuilder) {
        $loader = new Loader\YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

}
