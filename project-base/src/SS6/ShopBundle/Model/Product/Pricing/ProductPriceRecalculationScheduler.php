<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\Vat\Vat;
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
	public function scheduleRecalculatePriceForProduct(Product $product) {
		$this->products[$product->getId()] = $product;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function scheduleRecalculatePriceForVat(Vat $vat) {
		$this->productRepository->markProductsForPriceRecalculationByVat($vat);
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
	 * @return \SS6\ShopBundle\Model\Product\Product[][0]
	 */
	public function getProductsIteratorForRecalculation() {
		return $this->productRepository->getProductsForPriceRecalculationIterator();
	}

	public function cleanImmediatelyRecalculationSchedule() {
		$this->products = [];
	}

}
