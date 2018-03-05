<?php

namespace ShopSys\MigrationBundle\Tests\Unit\Component\Doctrine;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use ShopSys\MigrationBundle\Component\Doctrine\SchemaDiffFilter;

class SchemaDiffFilterTest extends TestCase
{
    public function testGetFilteredSchemaDiff()
    {
        $schemaDiff = new SchemaDiff();
        $testType = Type::getType('string');

        $tableDiff = new TableDiff('testTableDiff');
        $tableDiff->addedColumns = [new Column('testColumnName1', $testType)];
        $tableDiff->addedForeignKeys = [new ForeignKeyConstraint(['testColumnName2'], 'foreignTableName1', [])];
        $tableDiff->addedIndexes = [new Index('testIndexName1', ['testColumnName3'])];
        $tableDiff->changedColumns = [new Column('testColumnName4', $testType)];
        $tableDiff->changedForeignKeys = [new ForeignKeyConstraint(['testColumnName5'], 'foreignTableName2', [])];
        $tableDiff->changedIndexes = [new Index('testIndexName2', ['testColumnName6'])];
        $tableDiff->newName = 'newTableName';
        $tableDiff->removedColumns = [new Column('testColumnName7', $testType)];
        $tableDiff->removedForeignKeys = [new ForeignKeyConstraint(['testColumnName8'], 'foreignTableName3', [])];
        $tableDiff->removedIndexes = [new Index('testIndexName3', ['testColumnName9'])];
        $tableDiff->renamedColumns = [new Column('testColumnName10', $testType)];
        $tableDiff->renamedIndexes = [new Index('testIndexName4', ['testColumnName11'])];

        $schemaDiff->changedTables = [$tableDiff];
        $schemaDiff->changedSequences = [new Sequence('testSequence1')];
        $schemaDiff->newNamespaces = ['testNamespace1'];
        $schemaDiff->newSequences = [new Sequence('testSequence2')];
        $schemaDiff->newTables = [new Table('testTableName2')];
        $schemaDiff->orphanedForeignKeys = [new ForeignKeyConstraint(['testColumnName12'], 'foreignTableName3', [])];
        $schemaDiff->removedNamespaces = ['testNamespace2'];
        $schemaDiff->removedSequences = [new Sequence('testSequence3')];
        $schemaDiff->removedTables = [new Table('testTableName3')];

        $schemaDiffFilter = new SchemaDiffFilter();
        $filteredSchemaDiff = $schemaDiffFilter->getFilteredSchemaDiff($schemaDiff);

        foreach ($schemaDiff->changedTables as $index => $tableDiff) {
            $this->assertSame($tableDiff->addedColumns, $filteredSchemaDiff->changedTables[$index]->addedColumns);
            $this->assertSame($tableDiff->addedForeignKeys, $filteredSchemaDiff->changedTables[$index]->addedForeignKeys);
            $this->assertSame($tableDiff->addedIndexes, $filteredSchemaDiff->changedTables[$index]->addedIndexes);
            $this->assertSame($tableDiff->changedColumns, $filteredSchemaDiff->changedTables[$index]->changedColumns);
            $this->assertSame($tableDiff->changedForeignKeys, $filteredSchemaDiff->changedTables[$index]->changedForeignKeys);
            $this->assertSame($tableDiff->changedIndexes, $filteredSchemaDiff->changedTables[$index]->changedIndexes);
            $this->assertSame($tableDiff->newName, $filteredSchemaDiff->changedTables[$index]->newName);
            $this->assertSame($tableDiff->renamedColumns, $filteredSchemaDiff->changedTables[$index]->renamedColumns);
            $this->assertSame($tableDiff->renamedIndexes, $filteredSchemaDiff->changedTables[$index]->renamedIndexes);

            $this->assertEmpty($filteredSchemaDiff->changedTables[$index]->removedColumns);
            $this->assertEmpty($filteredSchemaDiff->changedTables[$index]->removedForeignKeys);
            $this->assertEmpty($filteredSchemaDiff->changedTables[$index]->removedIndexes);
        }

        $this->assertEmpty($filteredSchemaDiff->removedNamespaces);
        $this->assertEmpty($filteredSchemaDiff->removedSequences);
        $this->assertEmpty($filteredSchemaDiff->removedTables);

        $this->assertSame($schemaDiff->changedSequences, $filteredSchemaDiff->changedSequences);
        $this->assertSame($schemaDiff->newSequences, $filteredSchemaDiff->newSequences);
        $this->assertSame($schemaDiff->newTables, $filteredSchemaDiff->newTables);
        $this->assertSame($schemaDiff->orphanedForeignKeys, $filteredSchemaDiff->orphanedForeignKeys);
    }
}
