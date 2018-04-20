<?php

namespace Shopsys\MigrationBundle\Component\Doctrine;

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;

class DatabaseSchemaFacade
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\SchemaDiffFilter
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
        EntityManager $em,
        SchemaDiffFilter $schemaDiffFilter,
        Comparator $comparator,
        SchemaTool $schemaTool
    ) {
        $this->em = $em;
        $this->schemaDiffFilter = $schemaDiffFilter;
        $this->comparator = $comparator;
        $this->schemaTool = $schemaTool;
    }

    /**
     * @return string[]
     */
    public function getFilteredSchemaDiffSqlCommands()
    {
        $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();

        $databaseSchema = $this->em->getConnection()->getSchemaManager()->createSchema();
        $metadataSchema = $this->schemaTool->getSchemaFromMetadata($allMetadata);

        $schemaDiff = $this->comparator->compare($databaseSchema, $metadataSchema);
        $filteredSchemaDiff = $this->schemaDiffFilter->getFilteredSchemaDiff($schemaDiff);

        return $filteredSchemaDiff->toSaveSql($this->em->getConnection()->getDatabasePlatform());
    }
}
