<?php

namespace Shopsys\ProductFeed\ZboziBundle\Model\Product;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class ZboziProductDomainRepository
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
     * @return \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomain|null
     */
    public function findByProductIdAndDomainId($productId, $domainId)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(ZboziProductDomain::class, 'p')
            ->where('p.product = :productId')
            ->andWhere('p.domainId = :domainId')
            ->setParameter('productId', $productId)
            ->setParameter('domainId', $domainId);

        return $queryBuilder->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param int $productId
     * @return \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomain[]|null
     */
    public function findByProductId($productId)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(ZboziProductDomain::class, 'p')
            ->where('p.product = :productId')
            ->setParameter('productId', $productId);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param array $productsIds
     * @param int $domainId
     * @return \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomain[]
     */
    public function getZboziProductDomainsByProductsIdsDomainIdIndexedByProductId($productsIds, $domainId)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(ZboziProductDomain::class, 'p')
            ->where('p.domainId = :domainId')
            ->andWhere('p.product IN (:productIds)')
            ->setParameter('productIds', $productsIds)
            ->setParameter('domainId', $domainId);

        $result = $queryBuilder->getQuery()->execute();

        $indexedResult = [];
        foreach ($result as $zboziProductDomain) {
            $productId = $zboziProductDomain->getProduct()->getId();
            $indexedResult[$productId] = $zboziProductDomain;
        }

        return $indexedResult;
    }
}
