<?php

namespace Shopsys\ShopBundle\Tests\Test;

use Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\ShopBundle\Tests\Test\FunctionalTestCase;

abstract class DatabaseTestCase extends FunctionalTestCase {

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function getEntityManager() {
		return $this->getContainer()->get('doctrine.orm.entity_manager');
	}

	/**
	 * @return \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade
	 */
	protected function getEntityManagerFacade() {
		return $this->getContainer()->get(EntityManagerFacade::class);
	}

	protected function setUp() {
		parent::setUp();

		$this->getEntityManager()->beginTransaction();
	}

	protected function tearDown() {
		$this->getEntityManager()->rollback();

		parent::tearDown();
	}
}
