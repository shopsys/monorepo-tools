<?php

namespace Shopsys\ProductFeed\GoogleBundle\Model\Product;

use Doctrine\ORM\EntityManagerInterface;

class GoogleProductDomainRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * @param int $productId
     * @return \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomain[]
     */
    public function findByProductId($productId)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(GoogleProductDomain::class, 'p')
            ->where('p.product = :productId')
            ->setParameter('productId', $productId);

        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param int $productId
     * @param int $domainId
     * @return \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomain|null
     */
    public function findByProductIdAndDomainId($productId, $domainId)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('p')
            ->from(GoogleProductDomain::class, 'p')
            ->where('p.product = :productId')
            ->andWhere('p.domainId = :domainId')
            ->setParameter('productId', $productId)
            ->setParameter('domainId', $domainId);

        return $queryBuilder->getQuery()
            ->getOneOrNullResult();
    }
}
