<?php

namespace Tests\ShopBundle\Database\Component\Grid;

use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderDataSource;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\DatabaseTestCase;

class QueryBuilderDataSourceTest extends DatabaseTestCase
{
    public function testGetOneRow()
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $em \Doctrine\ORM\EntityManager */

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

        $this->assertSame(10, $count);
    }

    public function testGetRows()
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $em \Doctrine\ORM\EntityManager */

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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $em \Doctrine\ORM\EntityManager */

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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $em \Doctrine\ORM\EntityManager */

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
