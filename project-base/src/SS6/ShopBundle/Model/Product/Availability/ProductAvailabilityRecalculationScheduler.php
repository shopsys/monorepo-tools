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
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 */
	public function __construct(ProductRepository $productRepository) {
		$this->productRepository = $productRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function scheduleRecalculateAvailabilityForProduct(Product $product) {
		$this->products[$product->getId()] = $product;
	}

	public function scheduleRecalculateAvailabilityForAllProducts() {
		$this->productRepository->markAllProductsForAvailabilityRecalculation();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getProductsForImmediatelyRecalculation() {
		return $this->products;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product[][0]
	 */
	public function getProductsIteratorForRecalculation() {
		return $this->productRepository->getProductsForAvailabilityRecalculationIterator();
	}

	public function cleanImmediatelyRecalculationSchedule() {
		$this->products = [];
	}

}
