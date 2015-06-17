<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Paginator\PaginationResult;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterCountRepository;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\ProductVisibilityRepository;

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
	 * @var \SS6\ShopBundle\Model\Product\ProductVisibilityRepository
	 */
	private $productVisibilityRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountRepository
	 */
	private $productFilterCountRepository;

	/*
	 * @var \SS6\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository
	 */
	private $productAccessoryRepository;

	public function __construct(
		ProductRepository $productRepository,
		Domain $domain,
		ProductDetailFactory $productDetailFactory,
		CurrentCustomer $currentCustomer,
		CategoryRepository $categoryRepository,
		ProductVisibilityRepository $productVisibilityRepository,
		ProductFilterCountRepository $productFilterCountRepository,
		ProductAccessoryRepository $productAccessoryRepository
	) {
		$this->productRepository = $productRepository;
		$this->domain = $domain;
		$this->currentCustomer = $currentCustomer;
		$this->productDetailFactory = $productDetailFactory;
		$this->categoryRepository = $categoryRepository;
		$this->productVisibilityRepository = $productVisibilityRepository;
		$this->productFilterCountRepository = $productFilterCountRepository;
		$this->productAccessoryRepository = $productAccessoryRepository;
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
		$productAccessories = $this->productAccessoryRepository->getAllByProduct($product);
		$accessoriesVisibleOnDomain = [];

		foreach ($productAccessories as $productAccessory) {
			$accessoryVisibility = $this->productVisibilityRepository->getProductVisibility(
				$productAccessory->getAccessory(),
				$this->currentCustomer->getPricingGroup(),
				$this->domain->getId()
			);
			if ($accessoryVisibility->isVisible() && $productAccessory->getAccessory()->getCalculatedSellable()) {
				$accessoriesVisibleOnDomain[] = $productAccessory->getAccessory();
			}
		}

		return $this->productDetailFactory->getDetailsForProducts($accessoriesVisibleOnDomain);
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

		$paginationResult = $this->productRepository->getPaginationResultForListableInCategory(
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
	 * @param string|null $searchText
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
		$paginationResult = $this->productRepository->getPaginationResultForSearchListable(
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

	/**
	 * @param string|null $searchText
	 * @param int $limit
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getSearchAutocompleteProducts($searchText, $limit) {
		$emptyProductFilterData = new ProductFilterData();
		$orderingSetting = new ProductListOrderingSetting(ProductListOrderingSetting::ORDER_BY_NAME_ASC);

		$page = 1;

		$paginationResult = $this->productRepository->getPaginationResultForSearchListable(
			$searchText,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$emptyProductFilterData,
			$orderingSetting,
			$this->currentCustomer->getPricingGroup(),
			$page,
			$limit
		);

		return $paginationResult;
	}

	/**
	 * @param int $categoryId
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @return \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData
	 */
	public function getProductFilterCountDataInCategory(
		$categoryId,
		ProductFilterData $productFilterData
	) {
		return $this->productFilterCountRepository->getProductFilterCountDataInCategory(
			$this->categoryRepository->getById($categoryId),
			$this->domain->getId(),
			$this->domain->getLocale(),
			$productFilterData,
			$this->currentCustomer->getPricingGroup()
		);
	}

	/**
	 * @param string|null $searchText
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @return \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData
	 */
	public function getProductFilterCountDataForSearch(
		$searchText,
		ProductFilterData $productFilterData
	) {
		return $this->productFilterCountRepository->getProductFilterCountDataForSearch(
			$searchText,
			$this->domain->getId(),
			$this->domain->getLocale(),
			$productFilterData,
			$this->currentCustomer->getPricingGroup()
		);
	}

}
