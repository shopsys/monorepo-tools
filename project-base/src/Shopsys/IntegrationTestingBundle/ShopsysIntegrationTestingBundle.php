<?php

namespace Shopsys\IntegrationTestingBundle;

use Shopsys\IntegrationTestingBundle\DependencyInjection\Compiler\CollectClassNameByServiceIdMapCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShopsysIntegrationTestingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new CollectClassNameByServiceIdMapCompilerPass(),
            PassConfig::TYPE_BEFORE_REMOVING,
            -1
        );
    }
}
