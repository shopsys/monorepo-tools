<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\String\DatabaseSearching;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Component\Paginator\QueryPaginator;

class ProductSearchRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	public function __construct(
		EntityManager $em,
		ProductRepository $productRepository
	) {
		$this->em = $em;
		$this->productRepository = $productRepository;
	}

	/**
	 * @param int $domainId
	 * @param string $locale
	 * @param string $productName
	 * @param string $productCatnum
	 * @param int $page
	 * @param int $limit
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @return \SS6\ShopBundle\Component\Paginator\PaginationResult
	 */
	public function getPaginationResultVisibleByNameOrCatnum(
		$domainId,
		$locale,
		$productName,
		$productCatnum,
		$page,
		$limit
	) {
		$queryBuilder = $this->productRepository->getAllVisibleByDomainIdQueryBuilder($domainId);
		$this->productRepository->addTranslation($queryBuilder, $locale);

		$queryBuilder->andWhere('pt.name LIKE :productName OR p.catnum LIKE :productCatnum');
		$queryBuilder->setParameter('productName', '%' . DatabaseSearching::getLikeSearchString($productName) . '%');
		$queryBuilder->setParameter('productCatnum', '%' . DatabaseSearching::getLikeSearchString($productCatnum) . '%');

		$queryPaginator = new QueryPaginator($queryBuilder);

		return $queryPaginator->getResult($page, $limit);
	}

	
}
