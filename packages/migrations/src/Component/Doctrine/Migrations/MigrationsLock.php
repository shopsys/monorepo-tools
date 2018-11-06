<?php

namespace Shopsys\MigrationBundle\Component\Doctrine\Migrations;

use Symfony\Component\Yaml\Yaml;

class MigrationsLock
{
    /**
     * @var string
     */
    private $migrationsLockFilePath;

    /**
     * @var array|null
     */
    private $parsedMigrationsLock;

    /**
     * @param string $migrationsLockFilePath
     */
    public function __construct(string $migrationsLockFilePath)
    {
        $this->migrationsLockFilePath = $migrationsLockFilePath;
    }

    /**
     * @return string[]
     */
    public function getOrderedInstalledMigrationClasses(): array
    {
        $orderedInstalledMigrationClasses = [];
        foreach ($this->load() as $item) {
            if ($item['skip'] === false) {
                $orderedInstalledMigrationClasses[] = $item['class'];
            }
        }

        return $orderedInstalledMigrationClasses;
    }

    /**
     * @return string[]
     */
    public function getSkippedMigrationClasses(): array
    {
        $skippedMigrationClasses = [];
        foreach ($this->load() as $item) {
            if ($item['skip'] === true) {
                $skippedMigrationClasses[] = $item['class'];
            }
        }

        return $skippedMigrationClasses;
    }

    /**
     * @param \Doctrine\DBAL\Migrations\Version[] $migrationVersions
     */
    public function saveNewMigrations(array $migrationVersions)
    {
        $this->load();

        foreach ($migrationVersions as $migrationVersion) {
            if (!array_key_exists($migrationVersion->getVersion(), $this->parsedMigrationsLock)) {
                $this->parsedMigrationsLock[$migrationVersion->getVersion()] = [
                    'class' => get_class($migrationVersion->getMigration()),
                    'skip' => false,
                ];
            }
        }

        $this->save();
    }

    /**
     * @return array
     */
    private function load(): array
    {
        if ($this->parsedMigrationsLock === null) {
            $this->parsedMigrationsLock = [];

            if (file_exists($this->migrationsLockFilePath)) {
                $this->parsedMigrationsLock = Yaml::parseFile($this->migrationsLockFilePath);
            }
        }

        return $this->parsedMigrationsLock;
    }

    private function save(): void
    {
        $content = Yaml::dump($this->parsedMigrationsLock);

        file_put_contents($this->migrationsLockFilePath, $content);
    }
}
