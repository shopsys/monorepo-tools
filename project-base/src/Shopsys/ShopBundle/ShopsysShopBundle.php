<?php

namespace Shopsys\ShopBundle;

use Shopsys\ShopBundle\Component\Translation\Translator;
use Shopsys\ShopBundle\DependencyInjection\Compiler\CustomTranslationsCompilerPass;
use Shopsys\ShopBundle\Model\Security\Filesystem\FilemanagerAccess;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShopsysShopBundle extends Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container) {
        parent::build($container);

        $container->addCompilerPass(new CustomTranslationsCompilerPass());
    }

    public function boot() {
        parent::boot();

        $autoContainer = $this->container->get('shopsys.auto_services.auto_container');
        /* @var $autoContainer \Shopsys\AutoServicesBundle\Compiler\AutoContainer */
        $filemanagerAccess = $autoContainer->get(FilemanagerAccess::class);
        FilemanagerAccess::injectSelf($filemanagerAccess);

        $translator = $autoContainer->get(Translator::class);
        Translator::injectSelf($translator);
    }
}
