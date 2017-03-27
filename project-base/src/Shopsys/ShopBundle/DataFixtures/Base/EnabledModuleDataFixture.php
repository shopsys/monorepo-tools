<?php

namespace Shopsys\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Module\ModuleFacade;
use Shopsys\ShopBundle\Model\Module\ModuleList;

class EnabledModuleDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $moduleFacade = $this->get('shopsys.shop.module.module_facade');
        /* @var $moduleFacade \Shopsys\ShopBundle\Model\Module\ModuleFacade */
        $moduleFacade->setEnabled(ModuleList::PRODUCT_FILTER_COUNTS, true);
        $moduleFacade->setEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS, true);
    }
}
