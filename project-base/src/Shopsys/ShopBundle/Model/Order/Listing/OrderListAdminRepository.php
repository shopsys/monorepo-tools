<?php

namespace Shopsys\ShopBundle\Model\Order\Listing;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Model\Order\Order;

class OrderListAdminRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $locale
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderListQueryBuilder($locale)
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('
                o.id,
                o.number,
                o.domainId,
                o.createdAt,
                MAX(ost.name) AS statusName,
                o.totalPriceWithVat,
                (CASE WHEN o.companyName IS NOT NULL
                    THEN o.companyName
                    ELSE CONCAT(o.lastName, \' \', o.firstName)
                END) AS customerName')
            ->from(Order::class, 'o')
            ->where('o.deleted = :deleted')
            ->join('o.status', 'os')
            ->join('os.translations', 'ost', Join::WITH, 'ost.locale = :locale')
            ->groupBy('o.id')
            ->setParameter('deleted', false)
            ->setParameter('locale', $locale);

        return $queryBuilder;
    }
}
