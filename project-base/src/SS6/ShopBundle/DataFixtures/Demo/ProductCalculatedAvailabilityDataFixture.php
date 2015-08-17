<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;

class ProductCalculatedAvailabilityDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		// @codingStandardsIgnoreStart
		$productAvailabilityRecalculationScheduler = $this->get(ProductAvailabilityRecalculationScheduler::class);
		/* @var $productAvailabilityRecalculationScheduler \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler */
		$productAvailabilityRecalculator = $this->get(ProductAvailabilityRecalculator::class);
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
