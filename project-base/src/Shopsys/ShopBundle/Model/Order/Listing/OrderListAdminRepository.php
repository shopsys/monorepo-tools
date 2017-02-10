<?php

namespace Shopsys\ShopBundle\Model\Order\Listing;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Model\Localization\Localization;
use Shopsys\ShopBundle\Model\Order\Order;

class OrderListAdminRepository
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Localization\Localization
     */
    private $localization;

    public function __construct(EntityManager $em, Localization $localization) {
        $this->em = $em;
        $this->localization = $localization;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOrderListQueryBuilder() {
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
                            ELSE CONCAT(o.firstName, \' \', o.lastName)
                        END) AS customerName')
            ->from(Order::class, 'o')
            ->where('o.deleted = :deleted')
            ->join('o.status', 'os')
            ->join('os.translations', 'ost', Join::WITH, 'ost.locale = :locale')
            ->groupBy('o.id')
            ->setParameter('deleted', false)
            ->setParameter('locale', $this->localization->getLocale());

        return $queryBuilder;
    }
}
