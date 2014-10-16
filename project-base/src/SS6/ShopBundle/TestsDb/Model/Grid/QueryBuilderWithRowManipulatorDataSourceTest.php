<?php

namespace SS6\ShopBundle\TestsDb\Model\Grid;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Grid\QueryBuilderWithRowManipulatorDataSource;
use SS6\ShopBundle\Model\Product\Product;

class QueryBuilderWithRowManipulatorDataSourceTest extends DatabaseTestCase {

	public function testGetOneRow() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		$qb = $em->createQueryBuilder();
		$qb->select('p')
			->from(Product::class, 'p');

		$dataSource = new QueryBuilderWithRowManipulatorDataSource($qb, 'p.id', function ($row) {
			$row['newField'] = 'newValue';
			return $row;
		});

		$row = $dataSource->getOneRow($this->getReference('product_1'));

		$this->assertInternalType('array', $row);
		$this->assertArrayHasKey('newField', $row);
		$this->assertEquals('newValue', $row['newField']);
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

		$dataSource = new QueryBuilderWithRowManipulatorDataSource($qb, 'p.id', function ($row) {
			$row['newField'] = 'newValue' . $row['p']['id'];
			return $row;
		});

		$count = $dataSource->getTotalRowsCount();

		$this->assertEquals(10, $count);
	}

	public function testGetRows() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		$qb = $em->createQueryBuilder();
		$qb->select('p')
			->from(Product::class, 'p')
			->setMaxResults(5);

		$dataSource = new QueryBuilderWithRowManipulatorDataSource($qb, 'p.id', function ($row) {
			$row['newField'] = 'newValue' . $row['p']['id'];
			return $row;
		});

		$rows = $dataSource->getRows();
		$this->assertInternalType('array', $rows);
		$this->assertCount(5, $rows);

		foreach ($rows as $row) {
			$this->assertInternalType('array', $row);
			$this->assertArrayHasKey('newField', $row);
			$this->assertEquals('newValue' . $row['p']['id'], $row['newField']);
		}
	}

}
