<?php

namespace Shopsys\FrameworkBundle;

use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\CompilerPass\RegisterFriendlyUrlDataProviderCompilerPass;
use Shopsys\FrameworkBundle\DependencyInjection\Compiler\LazyRedisCompilerPass;
use Shopsys\FrameworkBundle\DependencyInjection\Compiler\RedisFacadeClientFilterCompilerPass;
use Shopsys\FrameworkBundle\DependencyInjection\Compiler\RegisterCronModulesCompilerPass;
use Shopsys\FrameworkBundle\DependencyInjection\Compiler\RegisterPluginCrudExtensionsCompilerPass;
use Shopsys\FrameworkBundle\DependencyInjection\Compiler\RegisterPluginDataFixturesCompilerPass;
use Shopsys\FrameworkBundle\DependencyInjection\Compiler\RegisterProductFeedConfigsCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShopsysFrameworkBundle extends Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterCronModulesCompilerPass());
        $container->addCompilerPass(new RegisterFriendlyUrlDataProviderCompilerPass());
        $container->addCompilerPass(new RegisterPluginCrudExtensionsCompilerPass());
        $container->addCompilerPass(new RegisterPluginDataFixturesCompilerPass());
        $container->addCompilerPass(new RegisterProductFeedConfigsCompilerPass());
        $container->addCompilerPass(new LazyRedisCompilerPass());
        $container->addCompilerPass(new RedisFacadeClientFilterCompilerPass(), PassConfig::TYPE_BEFORE_REMOVING);
    }
}
