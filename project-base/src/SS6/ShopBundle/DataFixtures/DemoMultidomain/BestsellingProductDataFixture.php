<?php

namespace SS6\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$bestsellingProductEditFacade = $this->get(BestsellingProductEditFacade::class);
		/* @var $bestsellingProductEditFacade \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade */

		$bestsellingProductEditFacade->edit(
			$this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PHOTO),
			2,
			[$this->getReference(ProductDataFixture::PRODUCT_PREFIX . '7')]
		);
	}

}
