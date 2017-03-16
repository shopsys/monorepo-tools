<?php

namespace Tests\ShopBundle\Database\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade;
use Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator;
use Shopsys\ShopBundle\Component\Sql\SqlQuoter;
use Tests\ShopBundle\Test\DatabaseTestCase;

class MultidomainEntityDataCreatorTest extends DatabaseTestCase
{
    public function testCopyAllMultidomainDataForNewDomainCopiesTestRow()
    {
        $em = $this->getContainer()->get(EntityManager::class);
        /* @var $em \Doctrine\ORM\EntityManager */

        $em->getConnection()->executeQuery('
            CREATE TABLE _test_table (
                domain_id int NOT NULL,
                title text NOT NULL,
                description text
            )
        ');

        $em->getConnection()->executeQuery("
            INSERT INTO _test_table (domain_id, title, description)
                VALUES (1, 'asdf', 'qwer')
        ");

        $multidomainEntityClassFinderFacadeMock = $this->getMockBuilder(MultidomainEntityClassFinderFacade::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllNotNullableColumnNamesIndexedByTableName'])
            ->getMock();

        $multidomainEntityClassFinderFacadeMock
            ->method('getAllNotNullableColumnNamesIndexedByTableName')
            ->willReturn([
                '_test_table' => ['title'],
            ]);

        $sqlQuoter = new SqlQuoter($em);

        $multidomainEntityDataCreator = new MultidomainEntityDataCreator($multidomainEntityClassFinderFacadeMock, $em, $sqlQuoter);

        $multidomainEntityDataCreator->copyAllMultidomainDataForNewDomain(1, 2);

        $results = $em->getConnection()->fetchAll('
            SELECT domain_id, title, description
            FROM _test_table
            ORDER BY domain_id
        ');

        $expectedResults = [
            [
                'domain_id' => 1,
                'title' => 'asdf',
                'description' => 'qwer',
            ],
            [
                'domain_id' => 2,
                'title' => 'asdf',
                'description' => null,
            ],
        ];

        $this->assertSame($expectedResults, $results);
    }

    public function testCopyAllMultidomainDataForNewDomainWithDomainIdDoesNotThrowDriverException()
    {
        $em = $this->getContainer()->get(EntityManager::class);
        /* @var $em \Doctrine\ORM\EntityManager */

        $em->getConnection()->executeQuery('
            CREATE TABLE _test_table (
                domain_id int NOT NULL,
                title text NOT NULL
            )
        ');

        $em->getConnection()->executeQuery("
            INSERT INTO _test_table (domain_id, title)
                VALUES (1, 'asdf')
        ");

        $multidomainEntityClassFinderFacadeMock = $this->getMockBuilder(MultidomainEntityClassFinderFacade::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllNotNullableColumnNamesIndexedByTableName'])
            ->getMock();

        $multidomainEntityClassFinderFacadeMock
            ->method('getAllNotNullableColumnNamesIndexedByTableName')
            ->willReturn([
                '_test_table' => ['domain_id', 'title'],
            ]);

        $sqlQuoter = new SqlQuoter($em);

        $multidomainEntityDataCreator = new MultidomainEntityDataCreator($multidomainEntityClassFinderFacadeMock, $em, $sqlQuoter);

        try {
            $multidomainEntityDataCreator->copyAllMultidomainDataForNewDomain(1, 2);
        } catch (\Doctrine\DBAL\Exception\DriverException $ex) {
            $this->fail('Exception not expected');
        }
    }
}
