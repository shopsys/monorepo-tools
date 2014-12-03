<?php

namespace SS6\ShopBundle\Model\Product\Detail;

use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Product;

class ProductDetailFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation $productPriceCalculation
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository $parameterRepository
	 */
	public function __construct(
		ProductPriceCalculation $productPriceCalculation,
		ParameterRepository $parameterRepository
	) {
		$this->productPriceCalculation = $productPriceCalculation;
		$this->parameterRepository = $parameterRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail
	 */
	public function getDetailForProduct(Product $product) {
		return new ProductDetail(
			$product,
			$this->getPrice($product),
			$this->getParameters($product)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail[]
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
		return $this->productPriceCalculation->calculatePrice($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	private function getParameters(Product $product) {
		$productParameterValues = $this->parameterRepository->getProductParameterValuesByProductEagerLoaded($product);
		foreach ($productParameterValues as $index => $productParameterValue) {
			$parameter = $productParameterValue->getParameter();

			if ($parameter->getName() === null) {
				unset($productParameterValues[$index]);
			}
		}

		return $productParameterValues;
	}
}
