<?php

namespace Shopsys\ShopBundle\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade;
use Shopsys\ShopBundle\Component\Sql\SqlQuoter;

class MultidomainEntityDataCreator
{

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinderFacade
     */
    private $multidomainEntityClassFinderFacade;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Sql\SqlQuoter
     */
    private $sqlQuoter;

    public function __construct(
        MultidomainEntityClassFinderFacade $multidomainEntityClassFinderFacade,
        EntityManager $em,
        SqlQuoter $sqlQuoter
    ) {
        $this->multidomainEntityClassFinderFacade = $multidomainEntityClassFinderFacade;
        $this->em = $em;
        $this->sqlQuoter = $sqlQuoter;
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
                return $columnName !== 'id' && $columnName !== 'domain_id';
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
        $quotedColumnNames = $this->sqlQuoter->quoteIdentifiers($columnNames);
        $quotedColumnNamesSql = implode(', ', $quotedColumnNames);
        $quotedTableName = $this->sqlQuoter->quoteIdentifier($tableName);
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
}
