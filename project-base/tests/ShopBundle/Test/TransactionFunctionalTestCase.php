<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Test;

abstract class TransactionFunctionalTestCase extends FunctionalTestCase
{
    /**
     * @return \Shopsys\FrameworkBundle\Component\EntityExtension\EntityManagerDecorator
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->getEntityManager()->beginTransaction();
    }

    protected function tearDown()
    {
        $this->getEntityManager()->rollback();

        parent::tearDown();
    }
}
