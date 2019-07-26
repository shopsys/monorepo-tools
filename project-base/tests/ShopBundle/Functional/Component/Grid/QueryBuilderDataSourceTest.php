<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Component\Grid;

use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class QueryBuilderDataSourceTest extends TransactionFunctionalTestCase
{
    public function testGetOneRow()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from(Product::class, 'p');

        $queryBuilderDataSource = new QueryBuilderDataSource($qb, 'p.id');

        $row = $queryBuilderDataSource->getOneRow($this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1'));

        $this->assertInternalType('array', $row);
        $this->assertArrayHasKey('p', $row);
    }

    public function testGetTotalRowsCount()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from(Product::class, 'p')
            ->where('p.id >= 1 AND p.id <= 10')
            ->setFirstResult(8)
            ->setMaxResults(5);

        $queryBuilderDataSource = new QueryBuilderDataSource($qb, 'p.id');

        $count = $queryBuilderDataSource->getTotalRowsCount();

        $this->assertSame(10, $count);
    }

    public function testGetRows()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from(Product::class, 'p')
            ->setMaxResults(5);

        $queryBuilderDataSource = new QueryBuilderDataSource($qb, 'p.id');

        $rows = $queryBuilderDataSource->getPaginatedRows()->getResults();
        $this->assertInternalType('array', $rows);
        $this->assertCount(5, $rows);

        foreach ($rows as $row) {
            $this->assertInternalType('array', $row);
            $this->assertArrayHasKey('p', $row);
        }
    }

    public function testGetRowsInAscOrder()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from(Product::class, 'p')
            ->setMaxResults(10);

        $queryBuilderDataSource = new QueryBuilderDataSource($qb, 'p.id');

        $rows = $queryBuilderDataSource->getPaginatedRows(null, 1, 'p.id', QueryBuilderDataSource::ORDER_ASC)->getResults();
        $this->assertCount(10, $rows);

        $lastId = null;
        foreach ($rows as $row) {
            if ($lastId === null) {
                $lastId = $row['p']['id'];
            } else {
                $this->assertGreaterThan($lastId, $row['p']['id']);
            }
        }
    }

    public function testGetRowsInDescOrder()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from(Product::class, 'p')
            ->setMaxResults(10);

        $queryBuilderDataSource = new QueryBuilderDataSource($qb, 'p.id');

        $rows = $queryBuilderDataSource->getPaginatedRows(null, 1, 'p.id', QueryBuilderDataSource::ORDER_DESC)->getResults();
        $this->assertCount(10, $rows);

        $lastId = null;
        foreach ($rows as $row) {
            if ($lastId === null) {
                $lastId = $row['p']['id'];
            } else {
                $this->assertLessThan($lastId, $row['p']['id']);
            }
        }
    }
}
