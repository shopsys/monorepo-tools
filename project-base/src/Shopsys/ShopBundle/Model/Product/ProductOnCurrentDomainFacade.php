<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Paginator\PaginationResult;
use Shopsys\ShopBundle\Form\Front\Product\ProductFilterFormType;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository;
use Shopsys\ShopBundle\Model\Product\Brand\BrandRepository;
use Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterCountRepository;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeService;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class ProductOnCurrentDomainFacade
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory
     */
    private $productDetailFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterCountRepository
     */
    private $productFilterCountRepository;

    /*
     * @var \Shopsys\ShopBundle\Model\Product\Accessory\ProductAccessoryRepository
     */
    private $productAccessoryRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandRepository
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
     * @return \Shopsys\ShopBundle\Model\Product\Detail\ProductDetail
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
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Detail\ProductDetail[]
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
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\Detail\ProductDetail[]
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
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingMode
     * @param int $page
     * @param int $limit
     * @param int $categoryId
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
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
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
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
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingMode
     * @param int $page
     * @param int $limit
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
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
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
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
     * @param \Shopsys\ShopBundle\Form\Front\Product\ProductFilterFormType $productFilterFormType
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterCountData
     */
    public function getProductFilterCountDataInCategory(
        $categoryId,
        ProductFilterFormType $productFilterFormType,
        ProductFilterData $productFilterData
    ) {
        $productsQueryBuilder = $this->productRepository->getListableInCategoryQueryBuilder(
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup(),
            $this->categoryRepository->getById($categoryId)
        );

        return $this->productFilterCountRepository->getProductFilterCountData(
            $productsQueryBuilder,
            $this->domain->getLocale(),
            $productFilterFormType,
            $productFilterData,
            $this->currentCustomer->getPricingGroup()
        );
    }

    /**
     * @param string|null $searchText
     * @param \Shopsys\ShopBundle\Form\Front\Product\ProductFilterFormType $productFilterFormType
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @return \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterCountData
     */
    public function getProductFilterCountDataForSearch(
        $searchText,
        ProductFilterFormType $productFilterFormType,
        ProductFilterData $productFilterData
    ) {
        $productsQueryBuilder = $this->productRepository->getListableBySearchTextQueryBuilder(
            $this->domain->getId(),
            $this->currentCustomer->getPricingGroup(),
            $this->domain->getLocale(),
            $searchText
        );

        return $this->productFilterCountRepository->getProductFilterCountData(
            $productsQueryBuilder,
            $this->domain->getLocale(),
            $productFilterFormType,
            $productFilterData,
            $this->currentCustomer->getPricingGroup()
        );
    }
}
