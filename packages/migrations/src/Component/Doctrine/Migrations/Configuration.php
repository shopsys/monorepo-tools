<?php

namespace Shopsys\MigrationBundle\Component\Doctrine\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration as DoctrineConfiguration;
use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\QueryWriter;
use Doctrine\DBAL\Migrations\Version;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\Exception\MethodIsNotAllowedException;

class Configuration extends DoctrineConfiguration
{
    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLock
     */
    private $migrationsLock;

    /**
     * @var \Doctrine\DBAL\Migrations\OutputWriter
     */
    private $outputWriter;

    /**
     * @var \Doctrine\DBAL\Migrations\Version[]
     */
    private $migrationVersions = null;

    /**
     * @param \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLock $migrationsLock
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Doctrine\DBAL\Migrations\OutputWriter $outputWriter
     * @param \Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface $finder
     * @param \Doctrine\DBAL\Migrations\QueryWriter|null $queryWriter
     */
    public function __construct(
        MigrationsLock $migrationsLock,
        Connection $connection,
        OutputWriter $outputWriter,
        MigrationFinderInterface $finder,
        QueryWriter $queryWriter = null
    ) {
        $this->migrationsLock = $migrationsLock;
        $this->outputWriter = $outputWriter;

        parent::__construct($connection, $outputWriter, $finder, $queryWriter);
    }

    /**
     * Gets the array of registered migration versions filtered and ordered by information in the migrations lock.
     * Version number is used as an index, because \Doctrine\DBAL\Migrations\Migration::migrate depends on it.
     * The internal parent::$migrations variable contains all registered migrations (even skipped ones) ordered by the timestamp.
     *
     * @return \Doctrine\DBAL\Migrations\Version[] $migrations
     */
    public function getMigrations()
    {
        if ($this->migrationVersions === null) {
            $this->migrationVersions = [];

            $foundMigrationVersionsByClass = [];
            /* @var $foundMigrationVersionsByClass \Doctrine\DBAL\Migrations\Version[] */
            foreach (parent::getMigrations() as $migrationVersion) {
                $class = get_class($migrationVersion->getMigration());
                $foundMigrationVersionsByClass[$class] = $migrationVersion;
            }

            foreach ($this->migrationsLock->getSkippedMigrationClasses() as $skippedMigrationClass) {
                if (array_key_exists($skippedMigrationClass, $foundMigrationVersionsByClass)) {
                    unset($foundMigrationVersionsByClass[$skippedMigrationClass]);
                } else {
                    $message = sprintf('WARNING: Migration version "%s" marked as skipped in migration lock file was not found!', $skippedMigrationClass);
                    $this->outputWriter->write($message);
                }
            }

            foreach ($this->migrationsLock->getOrderedInstalledMigrationClasses() as $installedMigrationClass) {
                if (array_key_exists($installedMigrationClass, $foundMigrationVersionsByClass)) {
                    $installedMigrationVersion = $foundMigrationVersionsByClass[$installedMigrationClass];
                    $this->migrationVersions[$installedMigrationVersion->getVersion()] = $installedMigrationVersion;
                    unset($migrationVersion);
                } else {
                    $message = sprintf('WARNING: Migration version "%s" marked as installed in migration lock file was not found!', $installedMigrationClass);
                    $this->outputWriter->write($message);
                }
            }

            foreach ($foundMigrationVersionsByClass as $newMigrationVersion) {
                $this->migrationVersions[$newMigrationVersion->getVersion()] = $newMigrationVersion;
            }
        }

        return $this->migrationVersions;
    }

    /**
     * Returns the array of migrations to executed based on the given direction and target version number.
     * Because of multiple migrations locations and the lock file, only complete UP migrations are allowed.
     *
     * @param string $direction the direction we are migrating (DOWN is not allowed)
     * @param string $to the version to migrate to (partial migrations are not allowed)
     *
     * @throws \Shopsys\MigrationBundle\Component\Doctrine\Migrations\Exception\MethodIsNotAllowedException
     * @return \Doctrine\DBAL\Migrations\Version[] $migrations the array of migrations we can execute
     */
    public function getMigrationsToExecute($direction, $to)
    {
        if ($direction === Version::DIRECTION_DOWN) {
            $this->throwMethodIsNotAllowedException('Migration down is not allowed.');
        }

        $migrationVersionsToExecute = [];
        $allMigrationVersions = $this->getMigrations();
        $migratedVersions = $this->getMigratedVersions();

        foreach ($allMigrationVersions as $version) {
            if ($to < $version->getVersion()) {
                $this->throwMethodIsNotAllowedException('Partial migration up in not allowed.');
            }

            if ($this->shouldExecuteMigration($version, $migratedVersions)) {
                $migrationVersionsToExecute[$version->getVersion()] = $version;
            }
        }

        return $migrationVersionsToExecute;
    }

    /**
     * @param \Doctrine\DBAL\Migrations\Version $version
     * @param \Doctrine\DBAL\Migrations\Version[] $migratedVersions
     * @return bool
     */
    private function shouldExecuteMigration(Version $version, array $migratedVersions)
    {
        $isVersionInstalled = in_array($version->getVersion(), $migratedVersions, true);

        return !$isVersionInstalled;
    }

    /**
     * @param string $message
     * @throws \Shopsys\MigrationBundle\Component\Doctrine\Migrations\Exception\MethodIsNotAllowedException
     */
    private function throwMethodIsNotAllowedException(string $message): void
    {
        $message .= ' Only up migration of all registered versions is supported because of multiple sources of migrations.';

        throw new MethodIsNotAllowedException($message);
    }
}
