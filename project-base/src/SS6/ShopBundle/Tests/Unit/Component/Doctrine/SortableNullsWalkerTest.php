<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Doctrine;

use Doctrine\ORM\Query;
use SS6\ShopBundle\Component\Doctrine\SortableNullsWalker;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

class SortableNullsWalkerTest extends FunctionalTestCase {

	public function testWalkOrderByItemAsc() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */
		$queryBuilder = $em->createQueryBuilder();
		/* @var $queryBuilder \Doctrine\ORM\QueryBuilder */

		$queryBuilder
			->select('p.id')
			->from(Product::class, 'p')
			->orderBy('p.id', 'ASC');

		$query = $queryBuilder->getQuery();
		$query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

		$this->assertStringEndsWith('ASC NULLS FIRST', $query->getSQL());
	}

	public function testWalkOrderByItemDesc() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */
		$queryBuilder = $em->createQueryBuilder();
		/* @var $queryBuilder \Doctrine\ORM\QueryBuilder */

		$queryBuilder
			->select('p.id')
			->from(Product::class, 'p')
			->orderBy('p.id', 'DESC');

		$query = $queryBuilder->getQuery();
		$query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

		$this->assertStringEndsWith('DESC NULLS LAST', $query->getSQL());
	}

	public function testWalkOrderByItemWithoutOrdering() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */
		$queryBuilder = $em->createQueryBuilder();
		/* @var $queryBuilder \Doctrine\ORM\QueryBuilder */

		$queryBuilder
			->select('p.id')
			->from(Product::class, 'p');

		$queryWithoutWalker = $queryBuilder->getQuery();
		$queryWithoutWalker->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

		$queryWithWalker = $queryBuilder->getQuery();

		$this->assertSame($queryWithoutWalker->getSQL(), $queryWithWalker->getSQL());
	}

}
