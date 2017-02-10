<?php

namespace Shopsys\ShopBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class ShopsysShopExtension extends ConfigurableExtension
{

    /**
     * {@inheritDoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container) {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('shopsys.router.locale_router_filepaths', $config['router']['locale_router_filepaths']);
        $container->setParameter('shopsys.router.friendly_url_router_filepath', $config['router']['friendly_url_router_filepath']);
    }
}
