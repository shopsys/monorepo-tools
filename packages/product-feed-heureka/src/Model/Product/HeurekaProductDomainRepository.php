<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Product;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class HeurekaProductDomainRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * @param int $productId
     * @param int $domainId
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomain|null
     */
    public function findByProductIdAndDomainId($productId, $domainId)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(HeurekaProductDomain::class, 'p')
            ->where('p.product = :productId')
            ->andWhere('p.domainId = :domainId')
            ->setParameter('productId', $productId)
            ->setParameter('domainId', $domainId);

        return $queryBuilder->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $productId
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomain[]|null
     */
    public function findByProductId($productId)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(HeurekaProductDomain::class, 'p')
            ->where('p.product = :productId')
            ->setParameter('productId', $productId);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param array $productsIds
     * @param int $domainId
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomain[]
     */
    public function getHeurekaProductDomainsByProductsIdsDomainIdIndexedByProductId($productsIds, $domainId)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(HeurekaProductDomain::class, 'p')
            ->where('p.domainId = :domainId')
            ->andWhere('p.product IN (:productIds)')
            ->setParameter('productIds', $productsIds)
            ->setParameter('domainId', $domainId);

        $result = $queryBuilder->getQuery()->execute();

        $indexedResult = [];
        foreach ($result as $heurekaProductDomain) {
            $productId = $heurekaProductDomain->getProduct()->getId();
            $indexedResult[$productId] = $heurekaProductDomain;
        }

        return $indexedResult;
    }
}
