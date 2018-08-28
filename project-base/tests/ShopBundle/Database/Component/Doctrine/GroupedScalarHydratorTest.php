<?php

namespace Tests\ShopBundle\Database\Component\Doctrine;

use Doctrine\ORM\Query\Expr\Join;
use Shopsys\FrameworkBundle\Component\Doctrine\GroupedScalarHydrator;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\ShopBundle\Model\Order\Order;
use Tests\ShopBundle\Test\DatabaseTestCase;

class GroupedScalarHydratorTest extends DatabaseTestCase
{
    public function testHydrateAllData()
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('o, oi')
            ->from(Order::class, 'o')
            ->join(OrderItem::class, 'oi', Join::WITH, 'oi.order = o')
            ->setMaxResults(1);

        $rows = $qb->getQuery()->execute(null, GroupedScalarHydrator::HYDRATION_MODE);
        $row = $rows[0];

        $this->assertInternalType('array', $row);

        $this->assertCount(2, $row);
        $this->assertArrayHasKey('o', $row);
        $this->assertArrayHasKey('oi', $row);

        $this->assertInternalType('array', $row['o']);
        $this->assertInternalType('array', $row['oi']);

        $this->assertArrayHasKey('id', $row['o']);
        $this->assertArrayHasKey('id', $row['oi']);
    }
}
