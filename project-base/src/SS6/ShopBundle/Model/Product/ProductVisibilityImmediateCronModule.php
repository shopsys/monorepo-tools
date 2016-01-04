<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;
use Symfony\Bridge\Monolog\Logger;

class ProductVisibilityImmediateCronModule implements CronModuleInterface {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityFacade
	 */
	private $productVisibilityFacade;

	public function __construct(ProductVisibilityFacade $productVisibilityFacade) {
		$this->productVisibilityFacade = $productVisibilityFacade;
	}

	/**
	 * @inheritdoc
	 */
	public function setLogger(Logger $logger) {

	}

	public function run() {
		$this->productVisibilityFacade->refreshProductsVisibilityForMarked();
	}

}
