<?php

namespace Shopsys\ShopBundle;

use Shopsys\ShopBundle\Component\Translation\Translator;
use Shopsys\ShopBundle\DependencyInjection\Compiler\RegisterCronModulesCompilerPass;
use Shopsys\ShopBundle\DependencyInjection\Compiler\RegisterPluginCrudExtensionsCompilerPass;
use Shopsys\ShopBundle\DependencyInjection\Compiler\RegisterPluginDataFixturesCompilerPass;
use Shopsys\ShopBundle\DependencyInjection\Compiler\RegisterProductFeedConfigsCompilerPass;
use Shopsys\ShopBundle\Model\Security\Filesystem\FilemanagerAccess;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShopsysShopBundle extends Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterCronModulesCompilerPass());
        $container->addCompilerPass(new RegisterPluginCrudExtensionsCompilerPass());
        $container->addCompilerPass(new RegisterPluginDataFixturesCompilerPass());
        $container->addCompilerPass(new RegisterProductFeedConfigsCompilerPass());
    }

    public function boot()
    {
        parent::boot();

        $filemanagerAccess = $this->container->get('shopsys.shop.security.filesystem.filemanager_access');
        FilemanagerAccess::injectSelf($filemanagerAccess);

        $translator = $this->container->get('translator');
        Translator::injectSelf($translator);
    }
}
