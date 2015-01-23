<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Paginator\PaginationResult;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\Filter\ProductSearchRepository;
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
	 * @var \SS6\ShopBundle\Model\Product\Filter\ProductSearchRepository
	 */
	private $productSearchRepository;

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
		ProductSearchRepository $productSearchRepository,
		ProductSearchService $productSearchService
	) {
		$this->productRepository = $productRepository;
		$this->domain = $domain;
		$this->currentCustomer = $currentCustomer;
		$this->productDetailFactory = $productDetailFactory;
		$this->categoryRepository = $categoryRepository;
		$this->productSearchRepository = $productSearchRepository;
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

		$paginationResult = $this->productRepository->getPaginationResultInCategory(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$orderingSetting,
			$page,
			$limit,
			$category,
			$this->currentCustomer->getPricingGroup(),
			$productFilterData
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
		$paginationResult = $this->productSearchRepository->getPaginationResultVisibleByNameOrCatnum(
			$this->domain->getId(),
			$this->domain->getLocale(),
			$searchText,
			$searchText,
			1,
			$limit
		);
		
		return $this->productSearchService->getSearchAutocompleteData(
			$paginationResult->getResults(),
			$paginationResult->getTotalCount()
		);
	}

}
