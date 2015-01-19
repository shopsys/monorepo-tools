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
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats = [];

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

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function scheduleRecalculatePriceForVat(Vat $vat) {
		$this->vats[$vat->getId()] = $vat;
	}

	public function scheduleRecalculatePriceForAllProducts() {
		$this->recalculateAll = true;
	}

	public function getProductsScheduledForRecalculation() {
		if ($this->recalculateAll) {
			return $this->productRepository->getAll();
		}

		foreach ($this->vats as $vat) {
			$products = $this->productRepository->getAllByVat($vat);
			foreach ($products as $product) {
				$this->products[$product->getId()] = $product;
			}
		}

		return $this->products;
	}

	public function cleanSchedule() {
		$this->products = [];
		$this->vats = [];
		$this->recalculateAll = false;
	}

}
