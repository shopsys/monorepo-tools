<?php

namespace SS6\ShopBundle\Tests\Database\Component\Doctrine;

use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Component\Doctrine\GroupedScalarHydrator;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class GroupedScalarHydratorTest extends DatabaseTestCase {

	public function testHydrateAllData() {
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
