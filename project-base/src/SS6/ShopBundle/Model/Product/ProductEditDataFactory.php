<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductDataFactory;

class ProductEditDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceFacade
	 */
	private $productManualInputPriceFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductDataFactory
	 */
	private $productDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\ProductPriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\InputPriceCalculation
	 */
	private $inputPriceCalculation;

	public function __construct(
		Domain $domain,
		ProductRepository $productRepository,
		ParameterRepository $parameterRepository,
		ProductManualInputPriceFacade $productManualInputPriceFacade,
		PricingGroupFacade $pricingGroupFacade,
		ProductDataFactory $productDataFactory,
		ProductPriceCalculation $productPriceCalculation,
		PricingSetting $pricingSetting,
		InputPriceCalculation $inputPriceCalculation
	) {
		$this->domain = $domain;
		$this->productRepository = $productRepository;
		$this->parameterRepository = $parameterRepository;
		$this->productManualInputPriceFacade = $productManualInputPriceFacade;
		$this->pricingGroupFacade = $pricingGroupFacade;
		$this->productDataFactory = $productDataFactory;
		$this->productPriceCalculation = $productPriceCalculation;
		$this->pricingSetting = $pricingSetting;
		$this->inputPriceCalculation = $inputPriceCalculation;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	public function createDefault() {
		$productEditData = new ProductEditData();
		$productEditData->productData = $this->productDataFactory->createDefault();

		$productParameterValuesData = [];
		$productEditData->parameters = $productParameterValuesData;

		$productEditData->manualInputPrices = [];
		$productEditData->seoTitles = [];
		$productEditData->seoMetaDescriptions = [];

		return $productEditData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	public function createFromProduct(Product $product) {
		$productEditData = $this->createDefault();
		$productDomains = $this->productRepository->getProductDomainsByProductIndexedByDomainId($product);
		$productEditData->productData = $this->productDataFactory->createFromProduct($product, $productDomains);

		$productParameterValuesData = [];
		$productParameterValues = $this->parameterRepository->getProductParameterValuesByProductEagerLoaded($product);
		foreach ($productParameterValues as $productParameterValue) {
			$productParameterValueData = new ProductParameterValueData();
			$productParameterValueData->setFromEntity($productParameterValue);
			$productParameterValuesData[] = $productParameterValueData;
		}
		$productEditData->parameters = $productParameterValuesData;

		$productEditData->manualInputPrices = $this->getManualInputPricesData($product);

		foreach ($productDomains as $productDomain) {
			$productEditData->seoTitles[$productDomain->getDomainId()] = $productDomain->getSeoTitle();
			$productEditData->seoMetaDescriptions[$productDomain->getDomainId()] = $productDomain->getSeoMetaDescription();
		}

		return $productEditData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return string[pricingGroupId]
	 */
	private function getManualInputPricesData(Product $product) {
		$manualInputPricesData = [];

		if ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_AUTO) {
			$inputPriceType = $this->pricingSetting->getInputPriceType();

			foreach ($this->pricingGroupFacade->getAll() as $pricingGroup) {
				$pricingGroupId = $pricingGroup->getId();

				$productPrice = $this->productPriceCalculation->calculatePrice($product, $pricingGroup);

				$manualInputPricesData[$pricingGroupId] = $this->inputPriceCalculation->getInputPrice(
					$inputPriceType,
					$productPrice->getPriceWithVat(),
					$product->getVat()->getPercent()
				);
			}
		} elseif ($product->getPriceCalculationType() === Product::PRICE_CALCULATION_TYPE_MANUAL) {
			$manualInputPrices = $this->productManualInputPriceFacade->getAllByProduct($product);
			foreach ($manualInputPrices as $manualInputPrice) {
				$pricingGroupId = $manualInputPrice->getPricingGroup()->getId();
				$manualInputPricesData[$pricingGroupId] = $manualInputPrice->getInputPrice();
			}
		}

		return $manualInputPricesData;
	}

}
