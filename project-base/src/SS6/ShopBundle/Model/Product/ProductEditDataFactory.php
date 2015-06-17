<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use SS6\ShopBundle\Form\UrlListType;
use SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository;
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

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
	 */
	private $friendlyUrlFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository
	 */
	private $productAccessoryRepository;

	public function __construct(
		ProductRepository $productRepository,
		ParameterRepository $parameterRepository,
		ProductDataFactory $productDataFactory,
		ProductInputPriceFacade $productInputPriceFacade,
		FriendlyUrlFacade $friendlyUrlFacade,
		ProductAccessoryRepository $productAccessoryRepository
	) {
		$this->productRepository = $productRepository;
		$this->parameterRepository = $parameterRepository;
		$this->productDataFactory = $productDataFactory;
		$this->productInputPriceFacade = $productInputPriceFacade;
		$this->friendlyUrlFacade = $friendlyUrlFacade;
		$this->productAccessoryRepository = $productAccessoryRepository;
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
		$productEditData->accessories = [];

		$productEditData->urls[UrlListType::TO_DELETE] = [];
		$productEditData->urls[UrlListType::MAIN_ON_DOMAINS] = [];
		$productEditData->urls[UrlListType::NEW_SLUGS_ON_DOMAINS] = [];

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
			$domainId = $productDomain->getDomainId();

			$productEditData->seoTitles[$domainId] = $productDomain->getSeoTitle();
			$productEditData->seoMetaDescriptions[$domainId] = $productDomain->getSeoMetaDescription();

			$productEditData->urls[UrlListType::MAIN_ON_DOMAINS][$domainId] =
				$this->friendlyUrlFacade->findMainFriendlyUrl($domainId, 'front_product_detail', $product->getId());
		}

		$productAccessories = [];
		foreach ($this->productAccessoryRepository->getAllByProduct($product) as $productAccessory) {
			$productAccessories[$productAccessory->getPosition()] = $productAccessory->getAccessory();
		}
		$productEditData->accessories = $productAccessories;

		return $productEditData;
	}

}
