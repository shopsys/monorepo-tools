<?php

namespace Shopsys\ShopBundle\Component\Domain;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Shopsys\ShopBundle\Component\Entity\EntityStringColumnsFinder;
use Shopsys\ShopBundle\Component\Sql\SqlQuoter;

class DomainUrlService
{
    /**
     * @var \Shopsys\ShopBundle\Component\Entity\EntityStringColumnsFinder
     */
    private $entityStringColumnsFinder;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Sql\SqlQuoter
     */
    private $sqlQuoter;

    public function __construct(
        EntityStringColumnsFinder $entityStringColumnsFinder,
        EntityManager $em,
        SqlQuoter $sqlQuoter
    ) {
        $this->entityStringColumnsFinder = $entityStringColumnsFinder;
        $this->em = $em;
        $this->sqlQuoter = $sqlQuoter;
    }

    /**
     * @param string $domainConfigUrl
     * @param string $domainSettingUrl
     */
    public function replaceUrlInStringColumns($domainConfigUrl, $domainSettingUrl)
    {
        $stringColumnNames = $this->getAllStringColumnNamesIndexedByTableName();
        foreach ($stringColumnNames as $tableName => $columnNames) {
            $urlReplacementSql = $this->getUrlReplacementSql($tableName, $columnNames, $domainSettingUrl, $domainConfigUrl);

            $this->em->createNativeQuery($urlReplacementSql, new ResultSetMapping())->execute();
        }
    }

    /**
     * @return string[tableName][]
     */
    private function getAllStringColumnNamesIndexedByTableName()
    {
        $classesMetadata = $this->em->getMetadataFactory()->getAllMetadata();

        return $this->entityStringColumnsFinder->getAllStringColumnNamesIndexedByTableName($classesMetadata);
    }

    /**
     * @param string $tableName
     * @param string[] $columnNames
     * @param string $domainSettingUrl
     * @param string $domainConfigUrl
     * @return string
     */
    private function getUrlReplacementSql($tableName, array $columnNames, $domainSettingUrl, $domainConfigUrl)
    {
        $sqlParts = [];
        $quotedTableName = $this->sqlQuoter->quoteIdentifier($tableName);
        $quotedColumnNames = $this->sqlQuoter->quoteIdentifiers($columnNames);
        $quotedDomainSettingUrl = $this->sqlQuoter->quote($domainSettingUrl);
        $quotedDomainConfigUrl = $this->sqlQuoter->quote($domainConfigUrl);
        foreach ($quotedColumnNames as $quotedName) {
            $sqlParts[] =
                $quotedName . ' = replace(' . $quotedName . ', ' . $quotedDomainSettingUrl . ', ' . $quotedDomainConfigUrl . ')';
        }

        return 'UPDATE ' . $quotedTableName . ' SET ' . implode(',', $sqlParts);
    }
}
