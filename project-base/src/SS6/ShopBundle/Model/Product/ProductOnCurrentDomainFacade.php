<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Paginator\PaginationResult;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository;
use SS6\ShopBundle\Model\Product\Brand\BrandRepository;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterCountRepository;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\Listing\ProductListOrderingModeService;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductOnCurrentDomainFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountRepository
	 */
	private $productFilterCountRepository;

	/*
	 * @var \SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository
	 */
	private $productAccessoryRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\BrandRepository
	 */
	private $brandRepository;

	public function __construct(
		ProductRepository $productRepository,
		Domain $domain,
		ProductDetailFactory $productDetailFactory,
		CurrentCustomer $currentCustomer,
		CategoryRepository $categoryRepository,
		ProductFilterCountRepository $productFilterCountRepository,
		ProductAccessoryRepository $productAccessoryRepository,
		BrandRepository $brandRepository
	) {
		$this->productRepository = $productRepository;
		$this->domain = $domain;
		$this->currentCustomer = $currentCustomer;
		$this->productDetailFactory = $productDetailFactory;
		$this->categoryRepository = $categoryRepository;
		$this->productFilterCountRepository = $productFilterCountRepository;
		$this->productAccessoryRepository = $productAccessoryRepository;
		$this->brandRepository = $brandRepository;
	}

	/**
	 * @param int $productId
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail
	 */
	public function getVisibleProductDetailById($productId) {
		$product = $this->productRepository->getVisible(
			$productId,
			$this->domain->getId(),
			$this->currentCustomer->getPricingGroup()
		);

		return $this->productDetailFactory->getDetailForProduct($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail[]
	 */
	public function getAccessoriesProductDetailsForProduct(Product $product) {
		$accessories = $this->productAccessoryRepository->getAllOfferedAccessoriesByProduct(
			$product,
			$this->domain->getId(),
			$this->currentCustomer->getPricingGroup()
		);

		return $this->productDetailFactory->getDetailsForProducts($accessories);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail[]
	 */
	public function getVariantsProductDetailsForProduct(Product $product) {
		$variants = $this->productRepository->getAllSellableVariantsByMainVariant(
			$product,
			$this->domain->getId(),
			$this->currentCustomer->getPricingGroup()
		);

		return $this->productDetailFactory->getDetailsForProducts($variants);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param string $orderingMode
	 * @param int $page
	 * @param int $limit
	 * @param int $categoryId
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginatedProductDetailsInCategory(
		ProductFilterData $productFilterData,
		$orderingMode,
		$page,
		$limit,
		$categoryId
	) {
		$category = $this->categoryRepository->getById($categoryId);

		$paginationResult = $this->productRepository->getPaginationResultForListableInCategory(
			$category,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$productFilterData,
			$orderingMode,
			$this->currentCustomer->getPricingGroup(),
			$page,
			$limit
		);
		$products = $paginationResult->getResults();

		return new PaginationResult(
			$paginationResult->getPage(),
			$paginationResult->getPageSize(),
			$paginationResult->getTotalCount(),
			$this->productDetailFactory->getDetailsForProducts($products)
		);
	}

	/**
	 * @param string $orderingMode
	 * @param int $page
	 * @param int $limit
	 * @param int $brandId
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginatedProductDetailsForBrand(
		$orderingMode,
		$page,
		$limit,
		$brandId
	) {
		$brand = $this->brandRepository->getById($brandId);

		$paginationResult = $this->productRepository->getPaginationResultForListableForBrand(
			$brand,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$orderingMode,
			$this->currentCustomer->getPricingGroup(),
			$page,
			$limit
		);
		$products = $paginationResult->getResults();

		return new PaginationResult(
			$paginationResult->getPage(),
			$paginationResult->getPageSize(),
			$paginationResult->getTotalCount(),
			$this->productDetailFactory->getDetailsForProducts($products)
		);
	}

	/**
	 * @param string|null $searchText
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param string $orderingMode
	 * @param int $page
	 * @param int $limit
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginatedProductDetailsForSearch(
		$searchText,
		ProductFilterData $productFilterData,
		$orderingMode,
		$page,
		$limit
	) {
		$paginationResult = $this->productRepository->getPaginationResultForSearchListable(
			$searchText,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$productFilterData,
			$orderingMode,
			$this->currentCustomer->getPricingGroup(),
			$page,
			$limit
		);
		$products = $paginationResult->getResults();

		return new PaginationResult(
			$paginationResult->getPage(),
			$paginationResult->getPageSize(),
			$paginationResult->getTotalCount(),
			$this->productDetailFactory->getDetailsForProducts($products)
		);
	}

	/**
	 * @param string|null $searchText
	 * @param int $limit
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getSearchAutocompleteProducts($searchText, $limit) {
		$emptyProductFilterData = new ProductFilterData();

		$page = 1;

		$paginationResult = $this->productRepository->getPaginationResultForSearchListable(
			$searchText,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$emptyProductFilterData,
			ProductListOrderingModeService::ORDER_BY_RELEVANCE,
			$this->currentCustomer->getPricingGroup(),
			$page,
			$limit
		);

		return $paginationResult;
	}

	/**
	 * @param int $categoryId
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand[] $brandFilterChoices
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag[] $flagFilterChoices
	 * @param \SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoice[] $parameterFilterChoices
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @return \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData
	 */
	public function getProductFilterCountDataInCategory(
		$categoryId,
		array $brandFilterChoices,
		array $flagFilterChoices,
		array $parameterFilterChoices,
		ProductFilterData $productFilterData
	) {
		return $this->productFilterCountRepository->getProductFilterCountDataInCategory(
			$this->categoryRepository->getById($categoryId),
			$this->domain->getId(),
			$this->domain->getLocale(),
			$brandFilterChoices,
			$flagFilterChoices,
			$parameterFilterChoices,
			$productFilterData,
			$this->currentCustomer->getPricingGroup()
		);
	}

	/**
	 * @param string|null $searchText
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand[] $brandFilterChoices
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag[] $flagFilterChoices
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @return \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData
	 */
	public function getProductFilterCountDataForSearch(
		$searchText,
		array $brandFilterChoices,
		array $flagFilterChoices,
		ProductFilterData $productFilterData
	) {
		return $this->productFilterCountRepository->getProductFilterCountDataForSearch(
			$searchText,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$brandFilterChoices,
			$flagFilterChoices,
			$productFilterData,
			$this->currentCustomer->getPricingGroup()
		);
	}

}
