<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use SS6\ShopBundle\Component\Cron\CronServiceInterface;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;

class ProductAvailabilityCronService implements CronServiceInterface {

	const PRODUCTS_AVAILABILITY_RECALCULATIONS_TIMELIMIT = 20;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator
	 */
	private $productAvailabilityRecalculator;

	public function __construct(ProductAvailabilityRecalculator $productAvailabilityRecalculator) {
		$this->productAvailabilityRecalculator = $productAvailabilityRecalculator;
	}

	public function run() {
		$timeStart = time();
		$this->productAvailabilityRecalculator->runScheduledRecalculations(function () use ($timeStart) {
			return time() - $timeStart < self::PRODUCTS_PRICES_RECALCULATIONS_TIMELIMIT;
		});
	}

}
