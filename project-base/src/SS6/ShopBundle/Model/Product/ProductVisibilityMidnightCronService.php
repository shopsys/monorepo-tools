<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Cron\CronServiceInterface;
use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;
use Symfony\Bridge\Monolog\Logger;

class ProductVisibilityMidnightCronService implements CronServiceInterface {

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
	public function run(Logger $logger) {
		$this->productVisibilityFacade->refreshProductsVisibility();
	}

}
