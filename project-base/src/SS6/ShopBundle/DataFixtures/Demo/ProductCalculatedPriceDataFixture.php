<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;

class ProductCalculatedPriceDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$productPriceRecalculationScheduler = $this->get('ss6.shop.product.pricing.product_price_recalculation_scheduler');
		/* @var $productPriceRecalculationScheduler \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler */
		$productPriceRecalculator = $this->get('ss6.shop.product.pricing.product_price_recalculator');
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

		$productPriceRecalculationScheduler->scheduleRecalculatePriceForAllProducts();
		$productPriceRecalculator->runScheduledRecalculations();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			ProductDataFixture::class,
			SettingValueDataFixture::class,
			VatDataFixture::class,
		);
	}

}
