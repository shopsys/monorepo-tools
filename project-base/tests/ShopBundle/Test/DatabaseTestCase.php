<?php

namespace Tests\ShopBundle\Test;

abstract class DatabaseTestCase extends FunctionalTestCase
{
    /**
     * @return \Doctrine\ORM\EntityManagerInterface
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
