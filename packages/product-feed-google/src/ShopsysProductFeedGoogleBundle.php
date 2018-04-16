<?php

namespace Shopsys\ProductFeed\GoogleBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShopsysProductFeedGoogleBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createAnnotationMappingDriver(
                [$this->getNamespace() . '\Model'],
                [$this->getPath() . '/Model']
            )
        );
    }
}
