<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use Symfony\Bridge\Monolog\Logger;

class ProductAvailabilityCronModule implements CronModuleInterface {

	const PRODUCTS_AVAILABILITY_RECALCULATIONS_TIMELIMIT = 20;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator
	 */
	private $productAvailabilityRecalculator;

	public function __construct(ProductAvailabilityRecalculator $productAvailabilityRecalculator) {
		$this->productAvailabilityRecalculator = $productAvailabilityRecalculator;
	}

	/**
	 * @inheritdoc
	 */
	public function run(Logger $logger) {
		$timeStart = time();
		$recalculated = $this->productAvailabilityRecalculator->runScheduledRecalculationsWhile(function () use ($timeStart) {
			return time() - $timeStart < self::PRODUCTS_AVAILABILITY_RECALCULATIONS_TIMELIMIT;
		});
		$logger->debug('Recalculated: ' . $recalculated);
	}

}
