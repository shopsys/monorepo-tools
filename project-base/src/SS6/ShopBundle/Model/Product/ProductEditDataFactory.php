<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductDataFactory;

class ProductEditDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductDataFactory
	 */
	private $productDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade
	 */
	private $productInputPriceFacade;

	public function __construct(
		ProductRepository $productRepository,
		ParameterRepository $parameterRepository,
		ProductDataFactory $productDataFactory,
		ProductInputPriceFacade $productInputPriceFacade
	) {
		$this->productRepository = $productRepository;
		$this->parameterRepository = $parameterRepository;
		$this->productDataFactory = $productDataFactory;
		$this->productInputPriceFacade = $productInputPriceFacade;
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

		$productEditData->manualInputPrices = $this->productInputPriceFacade->getManualInputPricesData($product);

		foreach ($productDomains as $productDomain) {
			$productEditData->seoTitles[$productDomain->getDomainId()] = $productDomain->getSeoTitle();
			$productEditData->seoMetaDescriptions[$productDomain->getDomainId()] = $productDomain->getSeoMetaDescription();
		}

		return $productEditData;
	}

}
