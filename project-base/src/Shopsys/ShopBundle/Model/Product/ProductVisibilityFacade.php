<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Product\ProductVisibilityRepository;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductVisibilityFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityRepository
	 */
	private $productVisibilityRepository;

	/**
	 * @var bool
	 */
	private $recalcVisibilityForMarked = false;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository
	 */
	public function __construct(ProductVisibilityRepository $productVisibilityRepository) {
		$this->productVisibilityRepository = $productVisibilityRepository;
	}

	public function refreshProductsVisibilityForMarkedDelayed() {
		$this->recalcVisibilityForMarked = true;
	}

	public function refreshProductsVisibility() {
		$this->productVisibilityRepository->refreshProductsVisibility();
	}

	public function refreshProductsVisibilityForMarked() {
		$this->productVisibilityRepository->refreshProductsVisibility(true);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 */
	public function markProductsForRecalculationAffectedByCategory(Category $category) {
		$this->productVisibilityRepository->markProductsForRecalculationAffectedByCategory($category);
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		if (!$event->isMasterRequest()) {
			return;
		}

		if ($this->recalcVisibilityForMarked) {
			$this->refreshProductsVisibilityForMarked();
		}
	}
}
