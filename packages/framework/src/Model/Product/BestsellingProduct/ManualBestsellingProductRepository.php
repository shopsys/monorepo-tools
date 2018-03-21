<?php

namespace Shopsys\FrameworkBundle\Model\Product\BestsellingProduct;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ManualBestsellingProductRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $productRepository)
    {
        $this->em = $entityManager;
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @return \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProduct[]
     */
    public function getByCategory($domainId, Category $category)
    {
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
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProduct[]
     */
    public function getOfferedByCategory($domainId, Category $category, PricingGroup $pricingGroup)
    {
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
     * @return int[]
     */
    public function getCountsIndexedByCategoryId($domainId)
    {
        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder
            ->select('c.id, COUNT(mbp) AS cnt')
            ->from(Category::class, 'c')
            ->leftJoin(ManualBestsellingProduct::class, 'mbp', Join::WITH, 'mbp.category = c AND mbp.domainId = :domainId')
            ->setParameter('domainId', $domainId)
            ->groupBy('c.id');

        $rows = $queryBuilder->getQuery()->execute();
        $countsIndexedByCategoryId = [];
        foreach ($rows as $row) {
            $countsIndexedByCategoryId[$row['id']] = $row['cnt'];
        }

        return $countsIndexedByCategoryId;
    }
}
