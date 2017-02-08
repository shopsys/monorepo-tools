<?php

namespace SS6\ShopBundle\Tests\Test;

use SS6\ShopBundle\Component\Doctrine\EntityManagerFacade;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;

abstract class DatabaseTestCase extends FunctionalTestCase {

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function getEntityManager() {
		return $this->getContainer()->get('doctrine.orm.entity_manager');
	}

	/**
	 * @return \SS6\ShopBundle\Component\Doctrine\EntityManagerFacade
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
