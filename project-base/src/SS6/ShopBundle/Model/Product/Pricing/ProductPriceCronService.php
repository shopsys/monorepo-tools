<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Component\Cron\CronServiceInterface;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;

class ProductPriceCronService implements CronServiceInterface {

	const PRODUCT_PRICE_RECALCULATIONS_TIMELIMIT = 20;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator
	 */
	private $productPriceRecalculator;

	public function __construct(ProductPriceRecalculator $productPriceRecalculator) {
		$this->productPriceRecalculator = $productPriceRecalculator;
	}

	public function run() {
		$timeStart = time();
		$this->productPriceRecalculator->runScheduledRecalculations(function () use ($timeStart) {
			return time() - $timeStart < self::PRODUCT_PRICE_RECALCULATIONS_TIMELIMIT;
		});
	}

}
