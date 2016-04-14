<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$bestsellingProductFacade = $this->get(BestsellingProductFacade::class);
		/* @var $bestsellingProductFacade \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade */

		$bestsellingProductFacade->edit(
			$this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PHOTO),
			1,
			[
				0 => $this->getReference('product_7'),
				2 => $this->getReference('product_8'),
				8 => $this->getReference('product_5'),
			]
		);

		$bestsellingProductFacade->edit(
			$this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::PHOTO),
			2,
			[$this->getReference('product_7')]
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
