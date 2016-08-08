<?php

namespace SS6\ShopBundle\Model\Category;

use SS6\ShopBundle\Model\Product\ProductVisibilityFacade;

class CategoryVisibilityRecalculationScheduler {

	/**
	 * @var bool
	 */
	private $recaluculate = false;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityFacade
	 */
	private $productVisibilityFacade;

	public function __construct(ProductVisibilityFacade $productVisibilityFacade) {
		$this->productVisibilityFacade = $productVisibilityFacade;
	}

	public function scheduleRecalculation() {
		$this->recaluculate = true;
		$this->productVisibilityFacade->refreshProductsVisibilityDelayed();
	}

	public function scheduleRecalculationWithoutDependencies() {
		$this->recaluculate = true;
	}

	/**
	 * @return bool
	 */
	public function isRecalculationScheduled() {
		return $this->recaluculate;
	}

}
