<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;

class ProductEditDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ParameterRepository
	 */
	private $parameterRepository;

	/**
	 * @var SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade
	 */
	private $productInputPriceFacade;

	public function __construct(
		Domain $domain,
		VatFacade $vatFacade,
		ProductRepository $productRepository,
		ParameterRepository $parameterRepository,
		ProductInputPriceFacade $productInputPriceFacade
	) {
		$this->domain = $domain;
		$this->vatFacade = $vatFacade;
		$this->productRepository = $productRepository;
		$this->parameterRepository = $parameterRepository;
		$this->productInputPriceFacade = $productInputPriceFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	public function createDefault() {
		$productEditData = new ProductEditData();
		$productEditData->productData = new ProductData();

		$productParameterValuesData = array();
		$productEditData->parameters = $productParameterValuesData;

		$productEditData->productData->vat = $this->vatFacade->getDefaultVat();
		$productEditData->productInputPrices = $this->productInputPriceFacade->getDefaultIndexedByPricingGroupId();

		return $productEditData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	public function createFromProduct(Product $product) {
		$productEditData = $this->createDefault();
		$productDomains = $this->productRepository->getProductDomainsByProduct($product);
		$productEditData->productData->setFromEntity($product, $productDomains);

		$productParameterValuesData = array();
		$productParameterValues = $this->parameterRepository->getProductParameterValuesByProductEagerLoaded($product);
		foreach ($productParameterValues as $productParameterValue) {
			$productParameterValueData = new ProductParameterValueData();
			$productParameterValueData->setFromEntity($productParameterValue);
			$productParameterValuesData[] = $productParameterValueData;
		}
		$productEditData->parameters = $productParameterValuesData;

		$productEditData->productInputPrices =  $this->productInputPriceFacade
			->getAllByProductIndexedByPricingGroupId($product);

		return $productEditData;
	}

}
