<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceFacade;
use SS6\ShopBundle\Model\Product\Product;

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
	 * @var SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceFacade
	 */
	private $productManualInputPriceFacade;

	public function __construct(
		Domain $domain,
		VatFacade $vatFacade,
		ProductRepository $productRepository,
		ParameterRepository $parameterRepository,
		ProductManualInputPriceFacade $productManualInputPriceFacade
	) {
		$this->domain = $domain;
		$this->vatFacade = $vatFacade;
		$this->productRepository = $productRepository;
		$this->parameterRepository = $parameterRepository;
		$this->productManualInputPriceFacade = $productManualInputPriceFacade;
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
		$productEditData->manualInputPrices = array();

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

		$manualInputPrices = $this->productManualInputPriceFacade->getAllByProduct($product);
		foreach ($manualInputPrices as $manualInputPrice) {
			$pricingGroupId = $manualInputPrice->getPricingGroup()->getId();
			$productEditData->manualInputPrices[$pricingGroupId] = $manualInputPrice->getInputPrice();
		}

		return $productEditData;
	}

}
