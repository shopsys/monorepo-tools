<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Paginator\PaginationResult;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\Filter\ProductSearchService;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductOnCurrentDomainFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
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
	 * @var \SS6\ShopBundle\Model\Product\Filter\ProductSearchService
	 */
	private $productSearchService;

	public function __construct(
		ProductRepository $productRepository,
		Domain $domain,
		ProductDetailFactory $productDetailFactory,
		CurrentCustomer $currentCustomer,
		CategoryRepository $categoryRepository,
		ProductSearchService $productSearchService
	) {
		$this->productRepository = $productRepository;
		$this->domain = $domain;
		$this->currentCustomer = $currentCustomer;
		$this->productDetailFactory = $productDetailFactory;
		$this->categoryRepository = $categoryRepository;
		$this->productSearchService = $productSearchService;
	}

	/**
	 * @param int $productId
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail
	 */
	public function getVisibleProductDetailById($productId) {
		$product = $this->productRepository->getVisibleByIdAndDomainId($productId, $this->domain->getId());

		return $this->productDetailFactory->getDetailForProduct($product);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Product\ProductListOrderingSetting $orderingSetting
	 * @param int $page
	 * @param int $limit
	 * @param int $categoryId
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginatedProductDetailsInCategory(
		ProductFilterData $productFilterData,
		ProductListOrderingSetting $orderingSetting,
		$page,
		$limit,
		$categoryId
	) {
		$category = $this->categoryRepository->getById($categoryId);

		$paginationResult = $this->productRepository->getPaginationResultForVisibleInCategory(
			$category,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$productFilterData,
			$orderingSetting,
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
	 * @param string $searchText
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Product\ProductListOrderingSetting $orderingSetting
	 * @param int $page
	 * @param int $limit
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginatedProductDetailsForSearch(
		$searchText,
		ProductFilterData $productFilterData,
		ProductListOrderingSetting $orderingSetting,
		$page,
		$limit
	) {
		$paginationResult = $this->productRepository->getPaginationResultForVisibleBySearchText(
			$searchText,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$productFilterData,
			$orderingSetting,
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

	public function getSearchAutocompleteData($searchText, $limit) {
		$emptyProductFilterData = new ProductFilterData();
		$orderingSetting = new ProductListOrderingSetting(ProductListOrderingSetting::ORDER_BY_NAME_ASC);

		$page = 1;

		$paginationResult = $this->productRepository->getPaginationResultForVisibleBySearchText(
			$searchText,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$emptyProductFilterData,
			$orderingSetting,
			$this->currentCustomer->getPricingGroup(),
			$page,
			$limit
		);

		return $this->productSearchService->getSearchAutocompleteData(
			$searchText,
			$paginationResult->getResults(),
			$paginationResult->getTotalCount()
		);
	}

}
