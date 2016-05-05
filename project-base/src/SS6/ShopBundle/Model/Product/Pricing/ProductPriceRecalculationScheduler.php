<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductPriceRecalculationScheduler {

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
	public function scheduleProductForImmediateRecalculation(Product $product) {
		$this->products[$product->getId()] = $product;
	}

	public function scheduleRecalculatePriceForAllProducts() {
		$this->productRepository->markAllProductsForPriceRecalculation();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getProductsForImmediatelyRecalculation() {
		return $this->products;
	}

	/**
	 * @return \Doctrine\ORM\Internal\Hydration\IterableResult|\SS6\ShopBundle\Model\Product\Product[][0]
	 */
	public function getProductsIteratorForRecalculation() {
		return $this->productRepository->getProductsForPriceRecalculationIterator();
	}

	public function cleanImmediatelyRecalculationSchedule() {
		$this->products = [];
	}

}
