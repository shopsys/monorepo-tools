<?php

namespace SS6\ShopBundle\Model\Product;

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
	private $recalcVisibility = false;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository
	 */
	public function __construct(ProductVisibilityRepository $productVisibilityRepository) {
		$this->productVisibilityRepository = $productVisibilityRepository;
	}

	public function refreshProductsVisibility() {
		$this->recalcVisibility = true;
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		if ($this->recalcVisibility) {
			$this->productVisibilityRepository->refreshProductsVisibility();
		}
	}
}
