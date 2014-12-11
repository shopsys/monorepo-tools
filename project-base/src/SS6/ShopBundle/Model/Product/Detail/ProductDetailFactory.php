<?php

namespace SS6\ShopBundle\Model\Product\Detail;

use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Product;

class ProductDetailFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculationForUser;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculationForUser
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation $productPriceCalculation
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository $parameterRepository
	 */
	public function __construct(
		ProductPriceCalculationForUser $productPriceCalculationForUser,
		ProductPriceCalculation $productPriceCalculation,
		ParameterRepository $parameterRepository
	) {
		$this->productPriceCalculationForUser = $productPriceCalculationForUser;
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
			$this->getBasePrice($product),
			$this->getSellingPrice($product),
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
	private function getBasePrice(Product $product) {
		return $this->productPriceCalculation->calculateBasePrice($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function getSellingPrice(Product $product) {
		return $this->productPriceCalculationForUser->calculatePriceForCurrentUser($product);
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
