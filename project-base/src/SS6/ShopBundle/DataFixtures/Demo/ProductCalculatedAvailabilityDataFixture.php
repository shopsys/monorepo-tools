<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;

class ProductCalculatedAvailabilityDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		// @codingStandardsIgnoreStart
		$productAvailabilityRecalculationScheduler = $this->get('ss6.shop.product.availability.product_availability_recalculation_scheduler');
		/* @var $productAvailabilityRecalculationScheduler \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler */
		$productAvailabilityRecalculator = $this->get('ss6.shop.product.availability.product_availability_recalculator');
		/* @var $productAvailabilityRecalculator \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */
		// @codingStandardsIgnoreEnd

		$productAvailabilityRecalculationScheduler->scheduleRecalculateAvailabilityForAllProducts();
		$productAvailabilityRecalculator->runScheduledRecalculations(function () {
			return true;
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			ProductDataFixture::class,
			SettingValueDataFixture::class,
		];
	}

}
