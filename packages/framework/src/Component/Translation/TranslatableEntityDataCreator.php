<?php

namespace Shopsys\FrameworkBundle\Component\Translation;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Prezent\Doctrine\Translatable\TranslationInterface;
use Shopsys\FrameworkBundle\Component\Doctrine\NotNullableColumnsFinder;
use Shopsys\FrameworkBundle\Component\Doctrine\SqlQuoter;

class TranslatableEntityDataCreator
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\NotNullableColumnsFinder
     */
    protected $notNullableColumnsFinder;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\SqlQuoter
     */
    protected $sqlQuoter;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\NotNullableColumnsFinder $notNullableColumnsFinder
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\SqlQuoter $sqlQuoter
     */
    public function __construct(
        EntityManagerInterface $em,
        NotNullableColumnsFinder $notNullableColumnsFinder,
        SqlQuoter $sqlQuoter
    ) {
        $this->em = $em;
        $this->notNullableColumnsFinder = $notNullableColumnsFinder;
        $this->sqlQuoter = $sqlQuoter;
    }

    /**
     * @param string $templateLocale
     * @param string $newLocale
     */
    public function copyAllTranslatableDataForNewLocale($templateLocale, $newLocale)
    {
        $notNullableColumns = $this->notNullableColumnsFinder->getAllNotNullableColumnNamesIndexedByTableName(
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
    protected function getAllTranslatableEntitiesMetadata()
    {
        $translatableEntitiesMetadata = [];
        /** @var \Doctrine\ORM\Mapping\ClassMetadata[] $allClassesMetadata */
        $allClassesMetadata = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($allClassesMetadata as $classMetadata) {
            if (is_subclass_of($classMetadata->getName(), TranslationInterface::class)) {
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
    protected function copyTranslatableDataForNewLocale($templateLocale, $newLocale, $tableName, array $columnNames)
    {
        $quotedColumnNames = $this->sqlQuoter->quoteIdentifiers($columnNames);
        $quotedColumnNamesSql = implode(', ', $quotedColumnNames);
        $quotedTableName = $this->sqlQuoter->quoteIdentifier($tableName);
        $query = $this->em->createNativeQuery(
            'INSERT INTO ' . $quotedTableName . ' (locale, ' . $quotedColumnNamesSql . ')
            SELECT :newLocale, ' . $quotedColumnNamesSql . '
            FROM ' . $quotedTableName . '
            WHERE locale = :templateLocale
            ON CONFLICT DO NOTHING',
            new ResultSetMapping()
        );
        $query->execute([
            'newLocale' => $newLocale,
            'templateLocale' => $templateLocale,
        ]);
    }
}
