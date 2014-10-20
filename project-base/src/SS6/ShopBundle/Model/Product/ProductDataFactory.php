<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;

class ProductDataFactory {

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
	 * @return \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function createDefault() {
		$productData = new ProductData();

		$productParameterValuesData = array();
		$productData->setParameters($productParameterValuesData);

		$hiddenData = array();
		foreach ($this->domain->getAll() as $domainConfig) {
			$hiddenData[$domainConfig->getId()] = false;
		}

		$productData->setHidden($hiddenData);

		$productData->setVat($this->vatFacade->getDefaultVat());

		return $productData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function createFromProduct(Product $product) {
		$productData = $this->createDefault();
		$productData->setFromEntity($product);

		$productParameterValuesData = array();

		$productParameterValues = $this->parameterRepository->findParameterValuesByProduct($product);
		foreach ($productParameterValues as $productParameterValue) {
			$productParameterValueData = new \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData();
			$productParameterValueData->setFromEntity($productParameterValue);
			$productParameterValuesData[] = $productParameterValueData;
		}

		$productData->setParameters($productParameterValuesData);

		$hidden = $productData->getHidden();
		foreach ($this->domain->getAll() as $domainConfig) {
			$productDomain = $this->productRepository->findProductDomainByProductAndDomainId($product, $domainConfig->getId());
			if ($productDomain !== null) {
				$hidden[$domainConfig->getId()] = $productDomain->isHidden();
			}
		}
		$productData->setHidden($hidden);

		return $productData;
	}

}
