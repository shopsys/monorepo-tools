<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Component\Doctrine\QueryBuilderService;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Paginator\QueryPaginator;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\Brand\Brand;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterRepository;
use Shopsys\ShopBundle\Model\Product\Listing\ProductListOrderingModeService;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductDomain;
use Shopsys\ShopBundle\Model\Product\ProductVisibility;
use Shopsys\ShopBundle\Model\Product\Search\ProductSearchRepository;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class ProductRepository
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterRepository
     */
    private $productFilterRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\QueryBuilderService
     */
    private $queryBuilderService;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Search\ProductSearchRepository
     */
    private $productSearchRepository;

    public function __construct(
        EntityManager $em,
        ProductFilterRepository $productFilterRepository,
        QueryBuilderService $queryBuilderService,
        Localization $localization,
        ProductSearchRepository $productSearchRepository
    ) {
        $this->em = $em;
        $this->productFilterRepository = $productFilterRepository;
        $this->queryBuilderService = $queryBuilderService;
        $this->localization = $localization;
        $this->productSearchRepository = $productSearchRepository;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getProductRepository() {
        return $this->em->getRepository(Product::class);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getProductDomainRepository() {
        return $this->em->getRepository(ProductDomain::class);
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Product\Product|null
     */
    public function findById($id) {
        return $this->getProductRepository()->find($id);
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllListableQueryBuilder($domainId, PricingGroup $pricingGroup) {
        $queryBuilder = $this->getAllOfferedQueryBuilder($domainId, $pricingGroup);
        $queryBuilder->andWhere('p.variantType != :variantTypeVariant')
            ->setParameter('variantTypeVariant', Product::VARIANT_TYPE_VARIANT);

        return $queryBuilder;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllSellableQueryBuilder($domainId, PricingGroup $pricingGroup) {
        $queryBuilder = $this->getAllOfferedQueryBuilder($domainId, $pricingGroup);
        $queryBuilder->andWhere('p.variantType != :variantTypeMain')
            ->setParameter('variantTypeMain', Product::VARIANT_TYPE_MAIN);

        return $queryBuilder;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllOfferedQueryBuilder($domainId, PricingGroup $pricingGroup) {
        $queryBuilder = $this->getAllVisibleQueryBuilder($domainId, $pricingGroup);
        $queryBuilder->andWhere('p.calculatedSellingDenied = FALSE');

        return $queryBuilder;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllVisibleQueryBuilder($domainId, PricingGroup $pricingGroup) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->join(ProductVisibility::class, 'prv', Join::WITH, 'prv.product = p.id')
            ->where('prv.domainId = :domainId')
                ->andWhere('prv.pricingGroup = :pricingGroup')
                ->andWhere('prv.visible = TRUE')
            ->orderBy('p.id');

        $queryBuilder->setParameter('domainId', $domainId);
        $queryBuilder->setParameter('pricingGroup', $pricingGroup);

        return $queryBuilder;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string $locale
     */
    public function addTranslation(QueryBuilder $queryBuilder, $locale) {
        $queryBuilder->addSelect('pt')
            ->join('p.translations', 'pt', Join::WITH, 'pt.locale = :locale');

        $queryBuilder->setParameter('locale', $locale);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param int $domainId
     */
    public function addDomain(QueryBuilder $queryBuilder, $domainId) {
        $queryBuilder->join(ProductDomain::class, 'pd', Join::WITH, 'pd.product = p AND pd.domainId = :domainId');
        $queryBuilder->setParameter('domainId', $domainId);
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getListableInCategoryQueryBuilder(
        $domainId,
        PricingGroup $pricingGroup,
        Category $category
    ) {
        $queryBuilder = $this->getAllListableQueryBuilder($domainId, $pricingGroup);
        $this->filterByCategory($queryBuilder, $category, $domainId);
        return $queryBuilder;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getListableForBrandQueryBuilder(
        $domainId,
        PricingGroup $pricingGroup,
        Brand $brand
    ) {
        $queryBuilder = $this->getAllListableQueryBuilder($domainId, $pricingGroup);
        $this->filterByBrand($queryBuilder, $brand);
        return $queryBuilder;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getSellableInCategoryQueryBuilder(
        $domainId,
        PricingGroup $pricingGroup,
        Category $category
    ) {
        $queryBuilder = $this->getAllSellableQueryBuilder($domainId, $pricingGroup);
        $this->filterByCategory($queryBuilder, $category, $domainId);
        return $queryBuilder;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOfferedInCategoryQueryBuilder(
        $domainId,
        PricingGroup $pricingGroup,
        Category $category
    ) {
        $queryBuilder = $this->getAllOfferedQueryBuilder($domainId, $pricingGroup);
        $this->filterByCategory($queryBuilder, $category, $domainId);

        return $queryBuilder;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $locale
     * @param string|null $searchText
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getListableBySearchTextQueryBuilder(
        $domainId,
        PricingGroup $pricingGroup,
        $locale,
        $searchText
    ) {
        $queryBuilder = $this->getAllListableQueryBuilder($domainId, $pricingGroup);

        $this->addTranslation($queryBuilder, $locale);
        $this->addDomain($queryBuilder, $domainId);

        $this->productSearchRepository->filterBySearchText($queryBuilder, $searchText);

        return $queryBuilder;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param int $domainId
     */
    private function filterByCategory(QueryBuilder $queryBuilder, Category $category, $domainId) {
        $queryBuilder->join('p.productCategoryDomains', 'pcd', Join::WITH, 'pcd.category = :category AND pcd.domainId = :domainId');
        $queryBuilder->setParameter('category', $category);
        $queryBuilder->setParameter('domainId', $domainId);
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     */
    private function filterByBrand(QueryBuilder $queryBuilder, Brand $brand) {
        $queryBuilder->andWhere('p.brand = :brand');
        $queryBuilder->setParameter('brand', $brand);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param int $domainId
     * @param string $locale
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingMode
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int $page
     * @param int $limit
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
     */
    public function getPaginationResultForListableInCategory(
        Category $category,
        $domainId,
        $locale,
        ProductFilterData $productFilterData,
        $orderingMode,
        PricingGroup $pricingGroup,
        $page,
        $limit
    ) {
        $queryBuilder = $this->getFilteredListableInCategoryQueryBuilder(
            $category,
            $domainId,
            $locale,
            $productFilterData,
            $pricingGroup
        );

        $this->applyOrdering($queryBuilder, $orderingMode, $pricingGroup, $locale);

        $queryPaginator = new QueryPaginator($queryBuilder);

        return $queryPaginator->getResult($page, $limit);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
     * @param int $domainId
     * @param string $orderingMode
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int $page
     * @param int $limit
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
     */
    public function getPaginationResultForListableForBrand(
        Brand $brand,
        $domainId,
        $locale,
        $orderingMode,
        PricingGroup $pricingGroup,
        $page,
        $limit
    ) {
        $queryBuilder = $this->getListableForBrandQueryBuilder(
            $domainId,
            $pricingGroup,
            $brand
        );

        $this->addTranslation($queryBuilder, $locale);
        $this->applyOrdering($queryBuilder, $orderingMode, $pricingGroup, $locale);

        $queryPaginator = new QueryPaginator($queryBuilder);

        return $queryPaginator->getResult($page, $limit);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param int $domainId
     * @param string $locale
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getFilteredListableInCategoryQueryBuilder(
        Category $category,
        $domainId,
        $locale,
        ProductFilterData $productFilterData,
        PricingGroup $pricingGroup
    ) {
        $queryBuilder = $this->getListableInCategoryQueryBuilder(
            $domainId,
            $pricingGroup,
            $category
        );

        $this->addTranslation($queryBuilder, $locale);
        $this->productFilterRepository->applyFiltering(
            $queryBuilder,
            $productFilterData,
            $pricingGroup
        );

        return $queryBuilder;
    }

    /**
     * @param string|null $searchText
     * @param int $domainId
     * @param string $locale
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param string $orderingMode
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int $page
     * @param int $limit
     * @return \Shopsys\ShopBundle\Component\Paginator\PaginationResult
     */
    public function getPaginationResultForSearchListable(
        $searchText,
        $domainId,
        $locale,
        ProductFilterData $productFilterData,
        $orderingMode,
        PricingGroup $pricingGroup,
        $page,
        $limit
    ) {
        $queryBuilder = $this->getFilteredListableForSearchQueryBuilder(
            $searchText,
            $domainId,
            $locale,
            $productFilterData,
            $pricingGroup
        );

        $this->productSearchRepository->addRelevance($queryBuilder, $searchText);
        $this->applyOrdering($queryBuilder, $orderingMode, $pricingGroup, $locale);

        $queryPaginator = new QueryPaginator($queryBuilder);

        return $queryPaginator->getResult($page, $limit);
    }

    /**
     * @param string|null $searchText
     * @param int $domainId
     * @param string $locale
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getFilteredListableForSearchQueryBuilder(
        $searchText,
        $domainId,
        $locale,
        ProductFilterData $productFilterData,
        PricingGroup $pricingGroup
    ) {
        $queryBuilder = $this->getListableBySearchTextQueryBuilder(
            $domainId,
            $pricingGroup,
            $locale,
            $searchText
        );

        $this->productFilterRepository->applyFiltering(
            $queryBuilder,
            $productFilterData,
            $pricingGroup
        );

        return $queryBuilder;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param string $orderingMode
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $locale
     */
    private function applyOrdering(
        QueryBuilder $queryBuilder,
        $orderingMode,
        PricingGroup $pricingGroup,
        $locale
    ) {
        switch ($orderingMode) {
            case ProductListOrderingModeService::ORDER_BY_NAME_ASC:
                $collation = $this->localization->getCollationByLocale($locale);
                $queryBuilder->orderBy("COLLATE(pt.name, '" . $collation . "')", 'asc');
                break;

            case ProductListOrderingModeService::ORDER_BY_NAME_DESC:
                $collation = $this->localization->getCollationByLocale($locale);
                $queryBuilder->orderBy("COLLATE(pt.name, '" . $collation . "')", 'desc');
                break;

            case ProductListOrderingModeService::ORDER_BY_PRICE_ASC:
                $this->queryBuilderService->addOrExtendJoin(
                    $queryBuilder,
                    ProductCalculatedPrice::class,
                    'pcp',
                    'pcp.product = p AND pcp.pricingGroup = :pricingGroup'
                );
                $queryBuilder->orderBy('pcp.priceWithVat', 'asc');
                $queryBuilder->setParameter('pricingGroup', $pricingGroup);
                break;

            case ProductListOrderingModeService::ORDER_BY_PRICE_DESC:
                $this->queryBuilderService->addOrExtendJoin(
                    $queryBuilder,
                    ProductCalculatedPrice::class,
                    'pcp',
                    'pcp.product = p AND pcp.pricingGroup = :pricingGroup'
                );
                $queryBuilder->orderBy('pcp.priceWithVat', 'desc');
                $queryBuilder->setParameter('pricingGroup', $pricingGroup);
                break;

            case ProductListOrderingModeService::ORDER_BY_RELEVANCE:
                $queryBuilder->orderBy('relevance', 'asc');
                break;

            case ProductListOrderingModeService::ORDER_BY_PRIORITY:
                $queryBuilder->orderBy('p.orderingPriority', 'desc');
                $collation = $this->localization->getCollationByLocale($locale);
                $queryBuilder->addOrderBy("COLLATE(pt.name, '" . $collation . "')", 'asc');
                break;

            default:
                $message = 'Product list ordering mode "' . $orderingMode . '" is not supported.';
                throw new \Shopsys\ShopBundle\Model\Product\Exception\InvalidOrderingModeException($message);
        }

        $queryBuilder->addOrderBy('p.id', 'asc');
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function getById($id) {
        $product = $this->findById($id);

        if ($product === null) {
            throw new \Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException('Product with ID ' . $id . ' does not exist.');
        }

        return $product;
    }

    /**
     * @param int $id
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function getVisible($id, $domainId, PricingGroup $pricingGroup) {
        $qb = $this->getAllVisibleQueryBuilder($domainId, $pricingGroup);
        $qb->andWhere('p.id = :productId');
        $qb->setParameter('productId', $id);

        $product = $qb->getQuery()->getOneOrNullResult();

        if ($product === null) {
            throw new \Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException();
        }

        return $product;
    }

    /**
     * @param int $id
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function getSellableById($id, $domainId, PricingGroup $pricingGroup) {
        $qb = $this->getAllSellableQueryBuilder($domainId, $pricingGroup);
        $qb->andWhere('p.id = :productId');
        $qb->setParameter('productId', $id);

        $product = $qb->getQuery()->getOneOrNullResult();

        if ($product === null) {
            throw new \Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException();
        }

        return $product;
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\ShopBundle\Model\Product\Product[][0]
     */
    public function getProductIteratorForReplaceVat() {
        $query = $this->em->createQuery('
            SELECT p
            FROM ' . Product::class . ' p
            JOIN p.vat v
            WHERE v.replaceWith IS NOT NULL
        ');

        return $query->iterate();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @return \Shopsys\ShopBundle\Model\Product\ProductDomain[]
     */
    public function getProductDomainsByProductIndexedByDomainId(Product $product) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('pd')
            ->from(ProductDomain::class, 'pd', 'pd.domainId')
            ->where('pd.product = :product')
            ->orderBy('pd.domainId', 'ASC');
        $queryBuilder->setParameter('product', $product);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Model\Product\ProductDomain[productId]
     */
    public function getProductDomainsByProductsAndDomainConfigIndexedByProductId(array $products, DomainConfig $domainConfig) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('pd')
            ->from(ProductDomain::class, 'pd')
            ->where('pd.product IN (:products)')->setParameter('products', $products)
            ->andWhere('pd.domainId = :domainId')->setParameter('domainId', $domainConfig->getId());

        $productDomainByProductId = [];
        foreach ($queryBuilder->getQuery()->execute() as $productDomain) {
            /* @var $productDomain \Shopsys\ShopBundle\Model\Product\ProductDomain */
            $productDomainByProductId[$productDomain->getProduct()->getId()] = $productDomain;
        }

        return $productDomainByProductId;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Product\ProductDomain|null
     */
    public function findProductDomainByProductAndDomainId(Product $product, $domainId) {
        return $this->getProductDomainRepository()->find([
            'product' => $product->getId(),
            'domainId' => $domainId,
        ]);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Product\ProductDomain
     */
    public function getProductDomainByProductAndDomainId(Product $product, $domainId) {
        $productDomain = $this->findProductDomainByProductAndDomainId($product, $domainId);
        if ($productDomain === null) {
            throw new \Shopsys\ShopBundle\Model\Product\Exception\ProductDomainNotFoundException();
        }

        return $productDomain;
    }

    public function markAllProductsForAvailabilityRecalculation() {
        $this->em
            ->createQuery('UPDATE ' . Product::class . ' p SET p.recalculateAvailability = TRUE
                WHERE p.recalculateAvailability = FALSE')
            ->execute();
    }

    public function markAllProductsForPriceRecalculation() {
        // Performance optimization:
        // Main variant price recalculation is triggered by variants visibility recalculation
        // and visibility recalculation is triggered by variant price recalculation.
        // Therefore main variant price recalculation is useless here.
        $this->em
            ->createQuery('UPDATE ' . Product::class . ' p SET p.recalculatePrice = TRUE
                WHERE p.variantType != :variantyTypeMain AND p.recalculateAvailability = FALSE')
            ->setParameter('variantyTypeMain', Product::VARIANT_TYPE_MAIN)
            ->execute();
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\ShopBundle\Model\Product\Product[][0]
     */
    public function getProductsForPriceRecalculationIterator() {
        return $this->getProductRepository()->createQueryBuilder('p')->where('p.recalculatePrice = TRUE')->getQuery()->iterate();
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult|\Shopsys\ShopBundle\Model\Product\Product[][0]|null
     */
    public function getProductsForAvailabilityRecalculationIterator() {
        return $this->getProductRepository()
            ->createQueryBuilder('p')
            ->where('p.recalculateAvailability = TRUE')
            ->getQuery()
            ->iterate();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $mainVariant
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getAllSellableVariantsByMainVariant(Product $mainVariant, $domainId, PricingGroup $pricingGroup) {
        $queryBuilder = $this->getAllSellableQueryBuilder($domainId, $pricingGroup);
        $queryBuilder
            ->andWhere('p.mainVariant = :mainVariant')
            ->setParameter('mainVariant', $mainVariant);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllSellableUsingStockInStockQueryBuilder($domainId, $pricingGroup) {
        $queryBuilder = $this->getAllSellableQueryBuilder($domainId, $pricingGroup);
        $queryBuilder
            ->andWhere('p.usingStock = TRUE')
            ->andWhere('p.stockQuantity > 0');

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $mainVariant
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getAtLeastSomewhereSellableVariantsByMainVariant(Product $mainVariant) {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->andWhere('p.calculatedVisibility = TRUE')
            ->andWhere('p.calculatedSellingDenied = FALSE')
            ->andWhere('p.variantType = :variantTypeVariant')->setParameter('variantTypeVariant', Product::VARIANT_TYPE_VARIANT)
            ->andWhere('p.mainVariant = :mainVariant')->setParameter('mainVariant', $mainVariant);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param int $domainId
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int[] $productIds
     * @return \Shopsys\ShopBundle\Model\Product\Product[]
     */
    public function getOfferedByIds($domainId, PricingGroup $pricingGroup, array $productIds) {
        if (count($productIds) === 0) {
            return [];
        }

        $queryBuilder = $this->getAllOfferedQueryBuilder($domainId, $pricingGroup);
        $queryBuilder->andWhere('p.id IN (:productIds)')->setParameter('productIds', $productIds);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param string $productCatnum
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function getOneByCatnumExcludeMainVariants($productCatnum) {
        $queryBuilder = $this->getProductRepository()->createQueryBuilder('p')
            ->andWhere('p.catnum = :catnum')
            ->andWhere('p.variantType != :variantTypeMain')
            ->setParameter('catnum', $productCatnum)
            ->setParameter('variantTypeMain', Product::VARIANT_TYPE_MAIN);
        $product = $queryBuilder->getQuery()->getOneOrNullResult();

        if ($product === null) {
            throw new \Shopsys\ShopBundle\Model\Product\Exception\ProductNotFoundException(
                'Product with catnum ' . $productCatnum . ' does not exist.'
            );
        }

        return $product;
    }
}
