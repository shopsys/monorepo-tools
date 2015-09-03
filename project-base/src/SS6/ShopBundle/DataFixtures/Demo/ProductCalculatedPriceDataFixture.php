<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;

class ProductCalculatedPriceDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$productPriceRecalculationScheduler = $this->get(ProductPriceRecalculationScheduler::class);
		/* @var $productPriceRecalculationScheduler \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler */
		$productPriceRecalculator = $this->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$productPriceRecalculationScheduler->scheduleRecalculatePriceForAllProducts();
		$productPriceRecalculator->runAllScheduledRecalculations();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			ProductDataFixture::class,
			SettingValueDataFixture::class,
			VatDataFixture::class,
		];
	}

}
