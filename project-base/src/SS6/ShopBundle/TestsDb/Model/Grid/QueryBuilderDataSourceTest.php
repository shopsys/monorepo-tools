<?php

namespace SS6\ShopBundle\TestsDb\Model\Grid;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Grid\QueryBuilderDataSource;
use SS6\ShopBundle\Model\Product\Product;

class QueryBuilderDataSourceTest extends DatabaseTestCase {

	public function testGetOneRow() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		$qb = $em->createQueryBuilder();
		$qb->select('p')
			->from(Product::class, 'p');

		$queryBuilderDataSource = new QueryBuilderDataSource($qb, 'p.id');

		$row = $queryBuilderDataSource->getOneRow($this->getReference('product_1'));

		$this->assertInternalType('array', $row);
		$this->assertArrayHasKey('p', $row);
	}

	public function testGetTotalRowsCount() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		$qb = $em->createQueryBuilder();
		$qb->select('p')
			->from(Product::class, 'p')
			->where('p.id >= 1 AND p.id <= 10')
			->setFirstResult(8)
			->setMaxResults(5);

		$queryBuilderDataSource = new QueryBuilderDataSource($qb, 'p.id');

		$count = $queryBuilderDataSource->getTotalRowsCount();

		$this->assertEquals(10, $count);
	}

	public function testGetRows() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		$qb = $em->createQueryBuilder();
		$qb->select('p')
			->from(Product::class, 'p')
			->setMaxResults(5);

		$queryBuilderDataSource = new QueryBuilderDataSource($qb, 'p.id');

		$rows = $queryBuilderDataSource->getRows();
		$this->assertInternalType('array', $rows);
		$this->assertCount(5, $rows);

		foreach ($rows as $row) {
			$this->assertInternalType('array', $row);
			$this->assertArrayHasKey('p', $row);
		}
	}

}
