<?php

namespace SS6\ShopBundle\Tests\Component\DoctrineWalker;

use Doctrine\ORM\Query;
use SS6\ShopBundle\Component\DoctrineWalker\SortableNullsWalker;
use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Product\Product;

class SortableNullsWalkerTest extends FunctionalTestCase {

	public function testWalkOrderByItem() {
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');
		/* @var $em \Doctrine\ORM\EntityManager */

		$queryBuilder = $em->createQueryBuilder();
		/* @var $queryBuilder \Doctrine\ORM\QueryBuilder */

		// minimal query for test
		$queryBuilder
			->select('p')
			->from(Product::class, 'p')
			->orderBy('p.id', 'ASC');

		$query = $queryBuilder->getQuery();
		/* @var $query \Doctrine\ORM\Query */

		$query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

		$sql = $query->getSQL();

		$this->assertStringEndsWith('ASC NULLS FIRST', $sql);
	}

}
