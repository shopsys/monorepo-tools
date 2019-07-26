<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Component\Doctrine;

use Doctrine\ORM\Query;
use Shopsys\FrameworkBundle\Component\Doctrine\SortableNullsWalker;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\FunctionalTestCase;

class SortableNullsWalkerTest extends FunctionalTestCase
{
    public function testWalkOrderByItemAsc()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $em->createQueryBuilder();

        $queryBuilder
            ->select('p.id')
            ->from(Product::class, 'p')
            ->orderBy('p.id', 'ASC');

        $query = $queryBuilder->getQuery();
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

        $this->assertStringEndsWith('ASC NULLS FIRST', $query->getSQL());
    }

    public function testWalkOrderByItemDesc()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $em->createQueryBuilder();

        $queryBuilder
            ->select('p.id')
            ->from(Product::class, 'p')
            ->orderBy('p.id', 'DESC');

        $query = $queryBuilder->getQuery();
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

        $this->assertStringEndsWith('DESC NULLS LAST', $query->getSQL());
    }

    public function testWalkOrderByItemWithoutOrdering()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $em->createQueryBuilder();

        $queryBuilder
            ->select('p.id')
            ->from(Product::class, 'p');

        $queryWithoutWalker = $queryBuilder->getQuery();
        $queryWithoutWalker->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

        $queryWithWalker = $queryBuilder->getQuery();

        $this->assertSame($queryWithoutWalker->getSQL(), $queryWithWalker->getSQL());
    }
}
