<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Symfony\Bridge\Monolog\Logger;

class ProductPriceCronModule implements CronModuleInterface {

	const PRODUCT_PRICE_RECALCULATIONS_TIMELIMIT = 20;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator
	 */
	private $productPriceRecalculator;

	public function __construct(ProductPriceRecalculator $productPriceRecalculator) {
		$this->productPriceRecalculator = $productPriceRecalculator;
	}

	/**
	 * @inheritdoc
	 */
	public function run(Logger $logger) {
		$timeStart = time();
		$recalculated = $this->productPriceRecalculator->runScheduledRecalculationsWhile(function () use ($timeStart) {
			return time() - $timeStart < self::PRODUCT_PRICE_RECALCULATIONS_TIMELIMIT;
		});

		$logger->debug('Recalculated: ' . $recalculated);
	}

}
