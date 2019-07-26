<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Component\Grid;

use Shopsys\FrameworkBundle\Component\Grid\QueryBuilderWithRowManipulatorDataSource;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class QueryBuilderWithRowManipulatorDataSourceTest extends TransactionFunctionalTestCase
{
    public function testGetOneRow()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from(Product::class, 'p');

        $dataSource = new QueryBuilderWithRowManipulatorDataSource($qb, 'p.id', function ($row) {
            $row['newField'] = 'newValue';
            return $row;
        });

        $row = $dataSource->getOneRow($this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1'));

        $this->assertInternalType('array', $row);
        $this->assertArrayHasKey('newField', $row);
        $this->assertSame('newValue', $row['newField']);
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

        $dataSource = new QueryBuilderWithRowManipulatorDataSource($qb, 'p.id', function ($row) {
            $row['newField'] = 'newValue' . $row['p']['id'];
            return $row;
        });

        $count = $dataSource->getTotalRowsCount();

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

        $dataSource = new QueryBuilderWithRowManipulatorDataSource($qb, 'p.id', function ($row) {
            $row['newField'] = 'newValue' . $row['p']['id'];
            return $row;
        });

        $rows = $dataSource->getPaginatedRows()->getResults();
        $this->assertInternalType('array', $rows);
        $this->assertCount(5, $rows);

        foreach ($rows as $row) {
            $this->assertInternalType('array', $row);
            $this->assertArrayHasKey('newField', $row);
            $this->assertSame('newValue' . $row['p']['id'], $row['newField']);
        }
    }
}
