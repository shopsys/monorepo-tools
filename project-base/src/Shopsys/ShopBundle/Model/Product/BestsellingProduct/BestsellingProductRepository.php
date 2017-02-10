<?php

namespace Shopsys\ShopBundle\Model\Product\BestsellingProduct;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Order\Item\OrderProduct;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatus;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\BestsellingProduct\ManualBestsellingProduct;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class BestsellingProductRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager, ProductRepository $productRepository) {
		$this->em = $entityManager;
		$this->productRepository = $productRepository;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 * @param int $domainId
	 * @return \Shopsys\ShopBundle\Model\Product\BestsellingProduct\ManualBestsellingProduct[]
	 */
	public function getManualBestsellingProductsByCategoryAndDomainId(Category $category, $domainId) {
		$queryBuilder = $this->em->createQueryBuilder()
			->select('bp')
			->from(ManualBestsellingProduct::class, 'bp', 'bp.position')
			->where('bp.category = :category')
			->andWhere('bp.domainId = :domainId')
			->setParameter('category', $category)
			->setParameter('domainId', $domainId);

		return $queryBuilder->getQuery()->execute();
	}

	/**
	 * @param int $domainId
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \Shopsys\ShopBundle\Model\Product\BestsellingProduct\ManualBestsellingProduct[]
	 */
	public function getOfferedManualBestsellingProducts($domainId, Category $category, PricingGroup $pricingGroup) {
		$queryBuilder = $this->productRepository->getAllOfferedQueryBuilder($domainId, $pricingGroup);

		$queryBuilder
			->select('bp')
			->join(ManualBestsellingProduct::class, 'bp', Join::WITH, 'bp.product = p')
			->andWhere('bp.category = :category')
			->andWhere('bp.domainId = prv.domainId')
			->setParameter('category', $category);

		return $queryBuilder->getQuery()->execute();
	}

	/**
	 * @param int $domainId
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param \DateTime $ordersCreatedAtLimit
	 * @param int $maxResults
	 * @return \Shopsys\ShopBundle\Model\Product\Product[]
	 */
	public function getOfferedAutomaticBestsellingProducts(
		$domainId,
		Category $category,
		PricingGroup $pricingGroup,
		DateTime $ordersCreatedAtLimit,
		$maxResults
	) {
		$queryBuilder = $this->productRepository->getOfferedInCategoryQueryBuilder($domainId, $pricingGroup, $category);

		$queryBuilder
			->addSelect('COUNT(op) AS HIDDEN orderCount')
			->join(ProductCalculatedPrice::class, 'pcp', Join::WITH, 'pcp.product = p')
			->join(OrderProduct::class, 'op', Join::WITH, 'op.product = p')
			->join('op.order', 'o')
			->join('o.status', 'os')
			->andWhere('pcp.pricingGroup = prv.pricingGroup')
			->andWhere('os.type = :orderStatusType')
			->setParameter('orderStatusType', OrderStatus::TYPE_DONE)
			->andWhere('o.createdAt >= :createdAt')
			->setParameter('createdAt', $ordersCreatedAtLimit)
			->orderBy('orderCount', 'DESC')
			->addOrderBy('pcp.priceWithVat', 'DESC')
			->groupBy('p.id, pcp.product, pcp.pricingGroup')
			->setMaxResults($maxResults);

		return $queryBuilder->getQuery()->execute();
	}

	/**
	 * @param int $domainId
	 * @return int[categoryId]
	 */
	public function getManualBestsellingProductCountsInCategories($domainId) {
		$queryBuilder = $this->em->createQueryBuilder();

		$queryBuilder
			->select('c.id, COUNT(mbp) AS cnt')
			->from(Category::class, 'c')
			->leftJoin(ManualBestsellingProduct::class, 'mbp', Join::WITH, 'mbp.category = c AND mbp.domainId = :domainId')
			->setParameter('domainId', $domainId)
			->groupBy('c.id');

		$rows = $queryBuilder->getQuery()->execute();
		$manualBestsellingProductCountsIndexedByCategoryId = [];
		foreach ($rows as $row) {
			$manualBestsellingProductCountsIndexedByCategoryId[$row['id']] = $row['cnt'];
		}

		return $manualBestsellingProductCountsIndexedByCategoryId;
	}

}
