<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Module\ModuleFacade;
use SS6\ShopBundle\Model\Module\ModuleList;

class EnabledModuleDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$moduleFacade = $this->get(ModuleFacade::class);
		/* @var $moduleFacade \SS6\ShopBundle\Model\Module\ModuleFacade */
		$moduleFacade->setEnabled(ModuleList::PRODUCT_FILTER_COUNTS, true);
		$moduleFacade->setEnabled(ModuleList::PRODUCT_STOCK_CALCULATIONS, true);
	}

}
