<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture as DemoCategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;
use Shopsys\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$bestsellingProductEditFacade = $this->get(BestsellingProductEditFacade::class);
		/* @var $bestsellingProductEditFacade \Shopsys\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade */

		$domainId = 2;
		$bestsellingProductEditFacade->edit(
			$this->getReference(DemoCategoryDataFixture::PREFIX . DemoCategoryDataFixture::PHOTO),
			$domainId,
			[$this->getReference(DemoProductDataFixture::PRODUCT_PREFIX . '7')]
		);
	}

}
