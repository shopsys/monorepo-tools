<?php

namespace Shopsys\MigrationBundle\Component\Doctrine;

use Doctrine\DBAL\Schema\Comparator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class DatabaseSchemaFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\SchemaDiffFilter
     */
    protected $schemaDiffFilter;

    /**
     * @var \Doctrine\DBAL\Schema\Comparator
     */
    protected $comparator;

    /**
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    protected $schemaTool;

    public function __construct(
        EntityManagerInterface $em,
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
