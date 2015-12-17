<?php

namespace SS6\ShopBundle\Component\Doctrine;

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use SS6\ShopBundle\Component\Doctrine\SchemaDiffFilter;

class DatabaseSchemaFacade {

	/**
	 * @var string
	 */
	private $defaultSchemaFilepath;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Doctrine\SchemaDiffFilter
	 */
	private $schemaDiffFilter;

	/**
	 * @var \Doctrine\DBAL\Schema\Comparator
	 */
	private $comparator;

	/**
	 * @var \Doctrine\ORM\Tools\SchemaTool
	 */
	private $schemaTool;

	public function __construct(
		$defaultSchemaFilepath,
		EntityManager $em,
		SchemaDiffFilter $schemaDiffFilter,
		Comparator $comparator,
		SchemaTool $schemaTool
	) {
		$this->defaultSchemaFilepath = $defaultSchemaFilepath;
		$this->em = $em;
		$this->schemaDiffFilter = $schemaDiffFilter;
		$this->comparator = $comparator;
		$this->schemaTool = $schemaTool;
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
	public function dropSchemaIfExists($schemaName) {
		$this->em->getConnection()->query('DROP SCHEMA IF EXISTS ' . $schemaName . ' CASCADE');
	}

	public function importDefaultSchema() {
		$connection = $this->em->getConnection();
		$handle = fopen($this->defaultSchemaFilepath, 'r');
		if ($handle) {
			$line = fgets($handle);
			while ($line !== false) {
				$connection->query($line);
				$line = fgets($handle);
			}
			fclose($handle);
		} else {
			$message = 'Failed to open file ' . $this->defaultSchemaFilepath . ' with default database schema.';
			throw new \SS6\ShopBundle\Component\Doctrine\Exception\DefaultSchemaImportException($message);
		}
	}

	/**
	 * @return string[]
	 */
	public function getFilteredSchemaDiffSqlCommands() {
		$allMetadata = $this->em->getMetadataFactory()->getAllMetadata();

		$databaseSchema = $this->em->getConnection()->getSchemaManager()->createSchema();
		$metadataSchema = $this->schemaTool->getSchemaFromMetadata($allMetadata);

		$schemaDiff = $this->comparator->compare($databaseSchema, $metadataSchema);
		$filteredSchemaDiff = $this->schemaDiffFilter->getFilteredSchemaDiff($schemaDiff);

		return $filteredSchemaDiff->toSaveSql($this->em->getConnection()->getDatabasePlatform());
	}

}
