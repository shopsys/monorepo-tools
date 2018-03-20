<?php

namespace Shopsys\FrameworkBundle\DependencyInjection;

use Shopsys\Environment;
use Shopsys\FrameworkBundle\Component\Grid\InlineEdit\GridInlineEditInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ShopsysFrameworkExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('paths.yml');

        if ($container->getParameter('kernel.environment') === Environment::ENVIRONMENT_TEST) {
            $loader->load('services_test.yml');
        }

        $container->registerForAutoconfiguration(GridInlineEditInterface::class)
            ->addTag('shopsys.grid_inline_edit');
    }
}
