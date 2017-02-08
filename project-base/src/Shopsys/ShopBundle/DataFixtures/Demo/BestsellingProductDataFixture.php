<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$bestsellingProductEditFacade = $this->get(BestsellingProductEditFacade::class);
		/* @var $bestsellingProductEditFacade \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductEditFacade */

		$bestsellingProductEditFacade->edit(
			$this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PHOTO),
			Domain::FIRST_DOMAIN_ID,
			[
				0 => $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '7'),
				2 => $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '8'),
				8 => $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '5'),
			]
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			ProductDataFixture::class,
			CategoryDataFixture::class,
		];
	}
}
