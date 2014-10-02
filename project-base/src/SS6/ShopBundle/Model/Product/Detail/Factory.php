<?php

namespace SS6\ShopBundle\Model\Product\Detail;

use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\PriceCalculation;
use SS6\ShopBundle\Model\Product\Product;

class Factory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\PriceCalculation
	 */
	private $priceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Product\PriceCalculation $priceCalculation
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository $parameterRepository
	 */
	public function __construct(
		PriceCalculation $priceCalculation,
		ParameterRepository $parameterRepository
	) {
		$this->priceCalculation = $priceCalculation;
		$this->parameterRepository = $parameterRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Detail\Detail
	 */
	public function getDetailForProduct(Product $product) {
		return new Detail(
			$product,
			$this->getPrice($product),
			$this->getParameters($product)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return \SS6\ShopBundle\Model\Product\Detail\Detail[]
	 */
	public function getDetailsForProducts(array $products) {
		$details = array();

		foreach ($products as $product) {
			$details[] = $this->getDetailForProduct($product);
		}

		return $details;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getPrice(Product $product) {
		return $this->priceCalculation->calculatePrice($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	private function getParameters(Product $product) {
		return $this->parameterRepository->findParameterValuesByProduct($product);
	}

}
