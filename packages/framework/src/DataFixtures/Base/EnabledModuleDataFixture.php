<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Module\ModuleFacade;
use Shopsys\FrameworkBundle\Model\Module\ModuleList;

class EnabledModuleDataFixture extends AbstractReferenceFixture
{
    /** @var \Shopsys\FrameworkBundle\Model\Module\ModuleFacade */
    private $moduleFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Module\ModuleFacade $moduleFacade
     */
    public function __construct(ModuleFacade $moduleFacade)
    {
        $this->moduleFacade = $moduleFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->moduleFacade->setEnabled(ModuleList::PRODUCT_FILTER_COUNTS, true);
        $this->moduleFacade->setEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS, true);
    }
}
