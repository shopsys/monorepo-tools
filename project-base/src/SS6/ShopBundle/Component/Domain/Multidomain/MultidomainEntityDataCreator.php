<?php

namespace SS6\ShopBundle\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade;

class MultidomainEntityDataCreator {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade
	 */
	private $multidomainEntityClassFinderFacade;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(MultidomainEntityClassFinderFacade $multidomainEntityClassFinderFacade, EntityManager $em) {
		$this->multidomainEntityClassFinderFacade = $multidomainEntityClassFinderFacade;
		$this->em = $em;
	}

	/**
	 * @param int $templateDomainId
	 * @param int $newDomainId
	 */
	public function copyAllMultidomainDataForNewDomain($templateDomainId, $newDomainId) {
		$columnNamesIndexedByTableName = $this->multidomainEntityClassFinderFacade
			->getAllNotNullableColumnNamesIndexedByTableName();
		foreach ($columnNamesIndexedByTableName as $tableName => $columnNames) {
			$columnNamesExcludingDomainId = array_filter($columnNames, function ($columnName) {
				return $columnName !== 'domain_id';
			});

			$this->copyMultidomainDataForNewDomain(
				$templateDomainId,
				$newDomainId,
				$tableName,
				$columnNamesExcludingDomainId
			);
		}
	}

	/**
	 * @param int $templateDomainId
	 * @param int $newDomainId
	 * @param string $tableName
	 * @param string[] $columnNames
	 */
	private function copyMultidomainDataForNewDomain($templateDomainId, $newDomainId, $tableName, array $columnNames) {
		$quotedColumnNamesSql = $this->getQuotedColumnNamesSql($columnNames);
		$quotedTableName = $this->quoteIdentifier($tableName);
		$query = $this->em->createNativeQuery('
			INSERT INTO ' . $quotedTableName . ' (domain_id, ' . $quotedColumnNamesSql . ')
			SELECT :newDomainId, ' . $quotedColumnNamesSql . '
			FROM ' . $quotedTableName . '
			WHERE domain_id = :templateDomainId',
			new ResultSetMapping()
		);
		$query->execute([
			'newDomainId' => $newDomainId,
			'templateDomainId' => $templateDomainId,
		]);
	}

	/**
	 * @param string[] $columnNames
	 * @return string
	 */
	private function getQuotedColumnNamesSql(array $columnNames) {
		$quotedColumnNames = array_map([$this, 'quoteIdentifier'], $columnNames);

		return implode(', ', $quotedColumnNames);
	}

	/**
	 * @param string $string
	 * @return string
	 */
	private function quoteIdentifier($string) {
		return $this->em->getConnection()->quoteIdentifier($string);
	}
}
