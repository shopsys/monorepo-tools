<?php

namespace SS6\ShopBundle\Model\Pricing;

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
	private $products = array();

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats = array();

	/**
	 * @var boolean
	 */
	private $recalculateAll = false;

	public function __construct(ProductRepository $productRepository) {
		$this->productRepository = $productRepository;
	}

	public function scheduleRecalculatePriceForProduct(Product $product) {
		$this->products[$product->getId()] = $product;
	}

	public function scheduleRecalculatePriceForVat(Vat $vat) {
		$this->vats[$vat->getId()] = $vat;
	}

	public function scheduleRecalculatePriceForAllProducts() {
		$this->recalculateAll = true;
	}

	public function getProductsScheduledForRecalculation() {
		foreach ($this->vats as $vat) {
			$products = $this->productRepository->getAllByVat($vat);
			foreach ($products as $product) {
				$this->products[$product->getId()] = $product;
			}
		}

		if ($this->recalculateAll) {
			$this->products = $this->productRepository->getAll();
		}

		return $this->products;
	}

	public function cleanSchedule() {
		$this->products = array();
		$this->vats = array();
		$this->recalculateAll = false;
	}

}
