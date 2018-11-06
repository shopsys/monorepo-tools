<?php

namespace Shopsys\MigrationBundle\Component\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;
use Doctrine\DBAL\Migrations\Finder\RecursiveRegexFinder;

class MigrationsFinder implements MigrationFinderInterface
{
    /**
     * @var \Doctrine\DBAL\Migrations\Finder\RecursiveRegexFinder
     */
    private $finder;

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator
     */
    private $migrationsLocator;

    /**
     * @param \Doctrine\DBAL\Migrations\Finder\RecursiveRegexFinder $finder
     * @param \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator $locator
     */
    public function __construct(RecursiveRegexFinder $finder, MigrationsLocator $locator)
    {
        $this->finder = $finder;
        $this->migrationsLocator = $locator;
    }

    /**
     * Finds all the migrations in all registered bundles using MigrationsLocator.
     * Passed parameters $directory and $namespace are ignored because of multiple sources of migrations.
     *
     * @param string $directory the passed $directory parameter is ignored
     * @param string|null $namespace the passed $namespace parameter is ignored
     * @return string[] an array of class names that were found with the version as keys
     */
    public function findMigrations($directory, $namespace = null)
    {
        $migrations = [];

        foreach ($this->migrationsLocator->getMigrationsLocations() as $location) {
            $migrations += $this->finder->findMigrations($location->getDirectory(), $location->getNamespace());
        }

        ksort($migrations, SORT_STRING);

        return $migrations;
    }
}
