<?php

namespace Shopsys\MigrationBundle\Component\Doctrine;

use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\TableDiff;

class SchemaDiffFilter
{
    /**
     * @param \Doctrine\DBAL\Schema\SchemaDiff $schemaDiff
     * @return \Doctrine\DBAL\Schema\SchemaDiff
     */
    public function getFilteredSchemaDiff(SchemaDiff $schemaDiff)
    {
        $filteredSchemaDiff = new SchemaDiff();

        $filteredSchemaDiff->changedSequences = $schemaDiff->changedSequences;
        $filteredSchemaDiff->newSequences = $schemaDiff->newSequences;
        $filteredSchemaDiff->newNamespaces = $schemaDiff->newNamespaces;
        $filteredSchemaDiff->newTables = $schemaDiff->newTables;
        $filteredSchemaDiff->orphanedForeignKeys = $schemaDiff->orphanedForeignKeys;

        foreach ($schemaDiff->changedTables as $tableDiff) {
            $filteredTableDiff = new TableDiff($tableDiff->name);
            $filteredTableDiff->addedColumns = $tableDiff->addedColumns;
            $filteredTableDiff->addedForeignKeys = $tableDiff->addedForeignKeys;
            $filteredTableDiff->addedIndexes = $tableDiff->addedIndexes;
            $filteredTableDiff->changedColumns = $tableDiff->changedColumns;
            $filteredTableDiff->changedForeignKeys = $tableDiff->changedForeignKeys;
            $filteredTableDiff->changedIndexes = $tableDiff->changedIndexes;
            $filteredTableDiff->renamedColumns = $tableDiff->renamedColumns;
            $filteredTableDiff->renamedIndexes = $tableDiff->renamedIndexes;
            $filteredTableDiff->newName = $tableDiff->newName;

            $filteredSchemaDiff->changedTables[] = $filteredTableDiff;
        }

        return $filteredSchemaDiff;
    }
}
