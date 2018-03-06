<?php

namespace Shopsys\FrameworkBundle\Model\Product\MassAction;

use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductMassActionRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->em = $entityManager;
    }

    /**
     * @param int[] $selectedProductIds
     * @param bool $hidden
     */
    public function setHidden(array $selectedProductIds, $hidden)
    {
        $updateQueryBuilder = $this->em->createQueryBuilder()
            ->update(Product::class, 'p')
            ->set('p.hidden', ':value')->setParameter('value', $hidden)
            ->set('p.recalculateVisibility', 'TRUE')
            ->where('p.id IN (:productIds)')->setParameter('productIds', $selectedProductIds);

        $updateQueryBuilder->getQuery()->execute();
    }
}
