<?php

namespace Shopsys\ShopBundle;

use Shopsys\FrameworkBundle\Component\Translation\Translator;
use Shopsys\FrameworkBundle\Model\Security\Filesystem\FilemanagerAccess;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShopsysShopBundle extends Bundle
{
    public function boot()
    {
        parent::boot();

        $filemanagerAccess = $this->container->get(FilemanagerAccess::class);
        FilemanagerAccess::injectSelf($filemanagerAccess);

        $translator = $this->container->get('translator');
        Translator::injectSelf($translator);
    }
}
