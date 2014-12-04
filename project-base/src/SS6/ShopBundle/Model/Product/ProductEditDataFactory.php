<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;

class ProductEditDataFactory {

	private $domain;

	private $vatFacade;

	private $productRepository;

	private $parameterRepository;

	public function __construct(
		Domain $domain,
		VatFacade $vatFacade,
		ProductRepository $productRepository,
		ParameterRepository $parameterRepository
	) {
		$this->domain = $domain;
		$this->vatFacade = $vatFacade;
		$this->productRepository = $productRepository;
		$this->parameterRepository = $parameterRepository;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	public function createDefault() {
		$productEditData = new ProductEditData();
		$productEditData->productData = new ProductData();

		$productParameterValuesData = array();
		$productEditData->parameters = $productParameterValuesData;

		$productEditData->productData->setVat($this->vatFacade->getDefaultVat());

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

		return $productEditData;
	}

}
