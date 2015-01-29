<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductAvailabilityRecalculationScheduler {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product[]
	 */
	private $products = [];

	/**
	 * @var boolean
	 */
	private $recalculateAll = false;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 */
	public function __construct(ProductRepository $productRepository) {
		$this->productRepository = $productRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function scheduleRecalculatePriceForProduct(Product $product) {
		$this->products[$product->getId()] = $product;
	}

	public function scheduleRecalculatePriceForAllProducts() {
		$this->recalculateAll = true;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProductsScheduledForRecalculation() {
		if ($this->recalculateAll) {
			return $this->productRepository->getAll();
		}

		return $this->products;
	}

	public function cleanSchedule() {
		$this->products = [];
		$this->recalculateAll = false;
	}

}
