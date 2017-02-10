<?php

namespace Shopsys\ShopBundle\Component\Doctrine;

use Doctrine\ORM\EntityManager;

class DatabaseSchemaFacade
{
    /**
     * @var string
     */
    private $defaultSchemaFilepath;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(
        $defaultSchemaFilepath,
        EntityManager $em
    ) {
        $this->defaultSchemaFilepath = $defaultSchemaFilepath;
        $this->em = $em;
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
            throw new \Shopsys\ShopBundle\Component\Doctrine\Exception\DefaultSchemaImportException($message);
        }
    }
}
