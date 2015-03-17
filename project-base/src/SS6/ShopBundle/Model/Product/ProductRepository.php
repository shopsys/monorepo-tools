<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\Doctrine\QueryBuilderService;
use SS6\ShopBundle\Component\Paginator\QueryPaginator;
use SS6\ShopBundle\Component\String\DatabaseSearching;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Filter\ParameterFilterRepository;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductListOrderingSetting;

class ProductRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ParameterFilterRepository
	 */
	private $parameterFilterRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ProductFilterRepository
	 */
	private $productFilterRepository;

	/**
	 * @var \SS6\ShopBundle\Component\DoctrineWalker\QueryBuilderService
	 */
	private $queryBuilderService;

	public function __construct(
		EntityManager $em,
		ParameterFilterRepository $parameterFilterRepository,
		ProductFilterRepository $productFilterRepository,
		QueryBuilderService $queryBuilderService
	) {
		$this->em = $em;
		$this->parameterFilterRepository = $parameterFilterRepository;
		$this->productFilterRepository = $productFilterRepository;
		$this->queryBuilderService = $queryBuilderService;
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
	 * @return \SS6\ShopBundle\Model\Product\Product|null
	 */
	public function findById($id) {
		return $this->getProductRepository()->find($id);
	}

	/**
	 * @param int $domainId
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getAllVisibleByDomainIdQueryBuilder($domainId) {
		$queryBuilder = $this->em->createQueryBuilder()
			->select('p')
			->from(Product::class, 'p')
			->join(ProductDomain::class, 'pd', Join::WITH, 'pd.product = p.id')
			->where('pd.domainId = :domainId')
				->andWhere('pd.visible = TRUE')
			->orderBy('p.id');

		$queryBuilder->setParameter('domainId', $domainId);

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
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getVisibleByDomainIdAndCategoryQueryBuilder(
		$domainId,
		Category $category
	) {
		$queryBuilder = $this->getAllVisibleByDomainIdQueryBuilder($domainId);
		$this->filterByCategory($queryBuilder, $category);
		return $queryBuilder;
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @param string|null $searchText
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getVisibleByDomainIdAndSearchTextQueryBuilder(
		$domainId,
		$locale,
		$searchText
	) {
		$queryBuilder = $this->getAllVisibleByDomainIdQueryBuilder($domainId);
		$this->addTranslation($queryBuilder, $locale);
		$this->filterBySearchText($queryBuilder, $searchText);
		return $queryBuilder;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 */
	private function filterByCategory(QueryBuilder $queryBuilder, Category $category) {
		$queryBuilder->join('p.categories', 'c', Join::WITH, 'c = :category');
		$queryBuilder->setParameter('category', $category);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param string|null $searchText
	 */
	private function filterBySearchText(QueryBuilder $queryBuilder, $searchText) {
		$queryBuilder->andWhere(
			'NORMALIZE(pt.name) LIKE NORMALIZE(:productName)'
			. ' OR NORMALIZE(p.catnum) LIKE NORMALIZE(:productCatnum)'
		);
		$queryBuilder->setParameter('productName', '%' . DatabaseSearching::getLikeSearchString($searchText) . '%');
		$queryBuilder->setParameter('productCatnum', '%' . DatabaseSearching::getLikeSearchString($searchText) . '%');
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param int $domainId
	 * @param string $locale
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Product\ProductListOrderingSetting $orderingSetting
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int $page
	 * @param int $limit
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginationResultForVisibleInCategory(
		Category $category,
		$domainId,
		$locale,
		ProductFilterData $productFilterData,
		ProductListOrderingSetting $orderingSetting,
		PricingGroup $pricingGroup,
		$page,
		$limit
	) {
		$queryBuilder = $this->getVisibleByDomainIdAndCategoryQueryBuilder($domainId, $category);

		$this->addTranslation($queryBuilder, $locale);
		$this->applyBasicFiltering($queryBuilder, $productFilterData, $pricingGroup);
		$this->parameterFilterRepository->filterByParameters($queryBuilder, $productFilterData->parameters);
		$this->applyOrdering($queryBuilder, $orderingSetting, $pricingGroup);

		$queryPaginator = new QueryPaginator($queryBuilder);

		return $queryPaginator->getResult($page, $limit);
	}

	/**
	 * @param string|null $searchText
	 * @param int $domainId
	 * @param string $locale
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Product\ProductListOrderingSetting $orderingSetting
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int $page
	 * @param int $limit
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginationResultForSearchVisible(
		$searchText,
		$domainId,
		$locale,
		ProductFilterData $productFilterData,
		ProductListOrderingSetting $orderingSetting,
		PricingGroup $pricingGroup,
		$page,
		$limit
	) {
		$queryBuilder = $this->getVisibleByDomainIdAndSearchTextQueryBuilder($domainId, $locale, $searchText);

		$this->applyBasicFiltering($queryBuilder, $productFilterData, $pricingGroup);
		$this->applyOrdering($queryBuilder, $orderingSetting, $pricingGroup);

		$queryPaginator = new QueryPaginator($queryBuilder);

		return $queryPaginator->getResult($page, $limit);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 */
	private function applyBasicFiltering(
		QueryBuilder $queryBuilder,
		ProductFilterData $productFilterData,
		PricingGroup $pricingGroup
	) {
		$this->productFilterRepository->filterByStock($queryBuilder, $productFilterData->inStock);
		$this->productFilterRepository->filterByPrice(
			$queryBuilder,
			$pricingGroup,
			$productFilterData->minimalPrice,
			$productFilterData->maximalPrice
		);
		$this->productFilterRepository->filterByFlags($queryBuilder, $productFilterData->flags);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\Product\ProductListOrderingSetting $orderingSetting
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 */
	private function applyOrdering(
		QueryBuilder $queryBuilder,
		ProductListOrderingSetting $orderingSetting,
		PricingGroup $pricingGroup
	) {
		switch ($orderingSetting->getOrderingMode()) {
			case ProductListOrderingSetting::ORDER_BY_NAME_ASC:
				$queryBuilder->orderBy('pt.name', 'asc');
				break;

			case ProductListOrderingSetting::ORDER_BY_NAME_DESC:
				$queryBuilder->orderBy('pt.name', 'desc');
				break;

			case ProductListOrderingSetting::ORDER_BY_PRICE_ASC:
				$this->queryBuilderService->addOrExtendJoin(
					$queryBuilder,
					ProductCalculatedPrice::class,
					'pcp',
					'pcp.product = p AND pcp.pricingGroup = :pricingGroup'
				);
				$queryBuilder->orderBy('pcp.priceWithVat', 'asc');
				$queryBuilder->setParameter('pricingGroup', $pricingGroup);
				break;

			case ProductListOrderingSetting::ORDER_BY_PRICE_DESC:
				$this->queryBuilderService->addOrExtendJoin(
					$queryBuilder,
					ProductCalculatedPrice::class,
					'pcp',
					'pcp.product = p AND pcp.pricingGroup = :pricingGroup'
				);
				$queryBuilder->orderBy('pcp.priceWithVat', 'desc');
				$queryBuilder->setParameter('pricingGroup', $pricingGroup);
				break;

			default:
				$message = 'Product list ordering mode "' . $orderingSetting->getOrderingMode() . '" is not supported.';
				throw new \SS6\ShopBundle\Model\Product\Exception\InvalidOrderingModeException($message);
		}

		$queryBuilder->addOrderBy('p.id', 'asc');
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getById($id) {
		$product = $this->findById($id);

		if ($product === null) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException('Product with ID ' . $id . ' does not exist.');
		}

		return $product;
	}

	/**
	 * @param int $id
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getVisibleByIdAndDomainId($id, $domainId) {
		$qb = $this->getAllVisibleByDomainIdQueryBuilder($domainId);
		$qb->andWhere('p.id = :productId');
		$qb->setParameter('productId', $id);

		$product = $qb->getQuery()->getOneOrNullResult();

		if ($product === null) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductNotFoundException();
		}

		return $product;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getAllByVat(Vat $vat) {
		return $this->getProductRepository()->findBy(['vat' => $vat]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\ProductDomain[]
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
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\ProductDomain|null
	 */
	public function findProductDomainByProductAndDomainId(Product $product, $domainId) {
		return $this->getProductDomainRepository()->find([
			'product' => $product->getId(),
			'domainId' => $domainId,
		]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\ProductDomain|null
	 */
	public function getProductDomainByProductAndDomainId(Product $product, $domainId) {
		$productDomain = $this->findProductDomainByProductAndDomainId($product, $domainId);
		if ($productDomain === null) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductDomainNotFoundException();
		}

		return $productDomain;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getVisibleProductsByDomainId($domainId) {
		return $this->getAllVisibleByDomainIdQueryBuilder($domainId)->getQuery()->getResult();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getAll() {
		return $this->getProductRepository()->findAll();
	}

}
