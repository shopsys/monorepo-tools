<?php

namespace SS6\ShopBundle\Component\Doctrine;

use Doctrine\ORM\EntityManager;

class DatabaseSchemaFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @param string $schemaName
	 */
	public function createSchema($schemaName) {
		$this->em->getConnection()->query('CREATE SCHEMA ' . $schemaName);
	}

	/**
	 * @param string $schemaName
	 */
	public function dropSchema($schemaName) {
		$this->em->getConnection()->query('DROP SCHEMA ' . $schemaName . ' CASCADE');
	}

}
