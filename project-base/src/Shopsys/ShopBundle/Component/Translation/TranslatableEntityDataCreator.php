<?php

namespace Shopsys\ShopBundle\Component\Translation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Prezent\Doctrine\Translatable\TranslationInterface;
use Shopsys\ShopBundle\Component\Entity\EntityNotNullableColumnsFinder;
use Shopsys\ShopBundle\Component\Sql\SqlQuoter;

class TranslatableEntityDataCreator
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Entity\EntityNotNullableColumnsFinder
     */
    private $entityNotNullableColumnsFinder;

    /**
     * @var \Shopsys\ShopBundle\Component\Sql\SqlQuoter
     */
    private $sqlQuoter;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(
        EntityManager $em,
        EntityNotNullableColumnsFinder $entityNotNullableColumnsFinder,
        SqlQuoter $sqlQuoter
    ) {
        $this->em = $em;
        $this->entityNotNullableColumnsFinder = $entityNotNullableColumnsFinder;
        $this->sqlQuoter = $sqlQuoter;
    }

    /**
     * @param string $templateLocale
     * @param string $newLocale
     */
    public function copyAllTranslatableDataForNewLocale($templateLocale, $newLocale) {
        $notNullableColumns = $this->entityNotNullableColumnsFinder->getAllNotNullableColumnNamesIndexedByTableName(
            $this->getAllTranslatableEntitiesMetadata()
        );
        foreach ($notNullableColumns as $tableName => $columnNames) {
            $columnNamesExcludingIdAndLocale = array_filter($columnNames, function ($columnName) {
                return $columnName !== 'id' && $columnName !== 'locale';
            });

            $this->copyTranslatableDataForNewLocale($templateLocale, $newLocale, $tableName, $columnNamesExcludingIdAndLocale);
        }
    }

    /**
     * @return \Doctrine\ORM\Mapping\ClassMetadata[]
     */
    private function getAllTranslatableEntitiesMetadata() {
        $translatableEntitiesMetadata = [];
        $allClassesMetadata = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($allClassesMetadata as $classMetadata) {
            /* @var $classMetadata \Doctrine\ORM\Mapping\ClassMetadata */
            if (is_subclass_of($classMetadata->name, TranslationInterface::class)) {
                $translatableEntitiesMetadata[] = $classMetadata;
            }
        }

        return $translatableEntitiesMetadata;
    }

    /**
     * @param string $templateLocale
     * @param string $newLocale
     * @param string $tableName
     * @param string[] $columnNames
     */
    private function copyTranslatableDataForNewLocale($templateLocale, $newLocale, $tableName, array $columnNames) {
        $quotedColumnNames = $this->sqlQuoter->quoteIdentifiers($columnNames);
        $quotedColumnNamesSql = implode(', ', $quotedColumnNames);
        $quotedTableName = $this->sqlQuoter->quoteIdentifier($tableName);
        $query = $this->em->createNativeQuery('
            INSERT INTO ' . $quotedTableName . ' (locale, ' . $quotedColumnNamesSql . ')
            SELECT :newLocale, ' . $quotedColumnNamesSql . '
            FROM ' . $quotedTableName . '
            WHERE locale = :templateLocale',
            new ResultSetMapping()
        );
        $query->execute([
            'newLocale' => $newLocale,
            'templateLocale' => $templateLocale,
        ]);
    }
}
