<?php

namespace SS6\ShopBundle\Model\Pricing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\PriceCalculation as ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ProductPriceRecalculator {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product[]
	 */
	private $products = array();

	/**
	 * @var boolean
	 */
	private $recalculateAll = false;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\PriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\ProductCalculatedPriceRepository
	 */
	private $productCalculatedPriceRepository;

	public function __construct(
		EntityManager $em,
		ProductRepository $productRepository,
		ProductPriceCalculation $productPriceCalculation,
		ProductCalculatedPriceRepository $productCalculatedPriceRepository
	) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productPriceCalculation = $productPriceCalculation;
		$this->productCalculatedPriceRepository = $productCalculatedPriceRepository;
	}

	public function scheduleRecalculatePriceForProduct(Product $product) {
		$this->products[$product->getId()] = $product;
	}

	public function scheduleRecalculatePriceForAllProducts() {
		$this->recalculateAll = true;
	}

	public function runScheduledRecalculations() {
		if ($this->recalculateAll) {
			$this->products = $this->productRepository->getAll();
		}

		foreach ($this->products as $product) {
			$price = $this->productPriceCalculation->calculatePrice($product);
			$this->productCalculatedPriceRepository->saveCalculatedPrice($product, $price->getPriceWithVat());
		}

		$this->em->flush();

		$this->products = array();
		$this->recalculateAll = false;
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
	 */
	public function onKernelResponse(FilterResponseEvent $event) {
		$this->runScheduledRecalculations();
	}

}
