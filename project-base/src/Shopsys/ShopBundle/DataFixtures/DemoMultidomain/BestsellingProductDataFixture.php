<?php

namespace SS6\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture as DemoCategoryDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$bestsellingProductEditFacade = $this->get(BestsellingProductEditFacade::class);
		/* @var $bestsellingProductEditFacade \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade */

		$domainId = 2;
		$bestsellingProductEditFacade->edit(
			$this->getReference(DemoCategoryDataFixture::PREFIX . DemoCategoryDataFixture::PHOTO),
			$domainId,
			[$this->getReference(DemoProductDataFixture::PRODUCT_PREFIX . '7')]
		);
	}

}
