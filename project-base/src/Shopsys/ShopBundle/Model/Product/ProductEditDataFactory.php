<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Image\ImageFacade;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository;
use SS6\ShopBundle\Model\Product\Parameter\ParameterRepository;
use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductDataFactory;

class ProductEditDataFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
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
	 * @var \SS6\ShopBundle\Model\Product\ProductDataFactory
	 */
	private $productDataFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade
	 */
	private $productInputPriceFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository
	 */
	private $productAccessoryRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct(
		Domain $domain,
		ProductRepository $productRepository,
		ParameterRepository $parameterRepository,
		ProductDataFactory $productDataFactory,
		ProductInputPriceFacade $productInputPriceFacade,
		FriendlyUrlFacade $friendlyUrlFacade,
		ProductAccessoryRepository $productAccessoryRepository,
		ImageFacade $imageFacade
	) {
		$this->domain = $domain;
		$this->productRepository = $productRepository;
		$this->parameterRepository = $parameterRepository;
		$this->productDataFactory = $productDataFactory;
		$this->productInputPriceFacade = $productInputPriceFacade;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->productAccessoryRepository = $productAccessoryRepository;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	public function createDefault() {
		$productEditData = new ProductEditData();
		$productEditData->productData = $this->productDataFactory->createDefault();

		$productParameterValuesData = [];
		$productEditData->parameters = $productParameterValuesData;

		$nullForAllDomains = $this->getNullForAllDomains();

		$productEditData->manualInputPrices = [];
		$productEditData->seoTitles = $nullForAllDomains;
		$productEditData->seoMetaDescriptions = $nullForAllDomains;
		$productEditData->descriptions = $nullForAllDomains;
		$productEditData->shortDescriptions = $nullForAllDomains;
		$productEditData->accessories = [];
		$productEditData->heurekaCpcValues = $nullForAllDomains;
		foreach ($this->domain->getAllIds() as $domainId) {
			$productEditData->showInZboziFeed[$domainId] = true;
		}
		$productEditData->zboziCpcValues = $nullForAllDomains;
		$productEditData->zboziCpcSearchValues = $nullForAllDomains;

		return $productEditData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\ProductEditData
	 */
	public function createFromProduct(Product $product) {
		$productEditData = $this->createDefault();

		$productEditData->productData = $this->productDataFactory->createFromProduct($product);
		$productEditData->parameters = $this->getParametersData($product);
		try {
			$productEditData->manualInputPrices = $this->productInputPriceFacade->getManualInputPricesData($product);
		} catch (\SS6\ShopBundle\Model\Product\Pricing\Exception\MainVariantPriceCalculationException $ex) {
			$productEditData->manualInputPrices = null;
		}
		$productEditData->accessories = $this->getAccessoriesData($product);
		$productEditData->imagePositions = $this->imageFacade->getImagesByEntityIndexedById($product, null);
		$productEditData->variants = $product->getVariants();

		$this->setMultidomainData($product, $productEditData);

		return $productEditData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Product[position]
	 */
	private function getAccessoriesData(Product $product) {
		$productAccessories = [];
		foreach ($this->productAccessoryRepository->getAllByProduct($product) as $productAccessory) {
			$productAccessories[$productAccessory->getPosition()] = $productAccessory->getAccessory();
		}

		return $productAccessories;
	}

	/**
	 * @param Product $product
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	private function getParametersData(Product $product) {
		$productParameterValuesData = [];
		$productParameterValues = $this->parameterRepository->getProductParameterValuesByProduct($product);
		foreach ($productParameterValues as $productParameterValue) {
			$productParameterValueData = new ProductParameterValueData();
			$productParameterValueData->setFromEntity($productParameterValue);
			$productParameterValuesData[] = $productParameterValueData;
		}

		return $productParameterValuesData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $productEditData
	 */
	private function setMultidomainData(Product $product, ProductEditData $productEditData) {
		$productDomains = $this->productRepository->getProductDomainsByProductIndexedByDomainId($product);
		foreach ($productDomains as $productDomain) {
			$domainId = $productDomain->getDomainId();

			$productEditData->seoTitles[$domainId] = $productDomain->getSeoTitle();
			$productEditData->seoMetaDescriptions[$domainId] = $productDomain->getSeoMetaDescription();
			$productEditData->descriptions[$domainId] = $productDomain->getDescription();
			$productEditData->shortDescriptions[$domainId] = $productDomain->getShortDescription();

			$productEditData->urls->mainOnDomains[$domainId] =
				$this->friendlyUrlFacade->findMainFriendlyUrl($domainId, 'front_product_detail', $product->getId());
			$productEditData->heurekaCpcValues[$domainId] = $productDomain->getHeurekaCpc();
			$productEditData->showInZboziFeed[$domainId] = $productDomain->getShowInZboziFeed();
			$productEditData->zboziCpcValues[$domainId] = $productDomain->getZboziCpc();
			$productEditData->zboziCpcSearchValues[$domainId] = $productDomain->getZboziCpcSearch();
		}
	}

	/**
	 * @return array
	 */
	private function getNullForAllDomains() {
		$nullForAllDomains = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$nullForAllDomains[$domainConfig->getId()] = null;
		}

		return $nullForAllDomains;
	}

}
