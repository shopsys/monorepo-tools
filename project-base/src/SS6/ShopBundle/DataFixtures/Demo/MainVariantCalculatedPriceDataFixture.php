<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductVisibilityDataFixture;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;

class MainVariantCalculatedPriceDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$productPriceRecalculator = $this->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$productPriceRecalculator->runScheduledRecalculations(function () {
			return true;
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			ProductVisibilityDataFixture::class,
		];
	}

}
