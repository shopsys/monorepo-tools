<?php

namespace Tests\MigrationBundle\Unit\Component\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\Version;
use PHPUnit\Framework\TestCase;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLock;
use Tests\MigrationBundle\Unit\Component\Doctrine\Migrations\Resources\Version20180101000001;
use Tests\MigrationBundle\Unit\Component\Doctrine\Migrations\Resources\Version20180101000002;
use Tests\MigrationBundle\Unit\Component\Doctrine\Migrations\Resources\Version20180101000003;
use Tests\MigrationBundle\Unit\Component\Doctrine\Migrations\Resources\Version20180101000004;

class MigrationsLockTest extends TestCase
{
    private const MIGRATION_LOCK_TEMPLATE = __DIR__ . '/Resources/migrations-lock.yml';
    private const MIGRATION_LOCK = __DIR__ . '/Resources/migrations-lock.yml.tmp';

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLock
     */
    private $migrationsLock;

    protected function setUp()
    {
        copy(self::MIGRATION_LOCK_TEMPLATE, self::MIGRATION_LOCK);
        $this->migrationsLock = $this->createNewMigrationsLock();
    }

    protected function tearDown()
    {
        unlink(self::MIGRATION_LOCK);
    }

    /**
     * @return \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLock
     */
    private function createNewMigrationsLock(): MigrationsLock
    {
        return new MigrationsLock(self::MIGRATION_LOCK);
    }

    public function testGetSkippedMigrationClasses()
    {
        $skippedMigrationClasses = $this->migrationsLock->getSkippedMigrationClasses();

        $this->assertCount(1, $skippedMigrationClasses);
        $this->assertContains(Version20180101000002::class, $skippedMigrationClasses);
    }

    public function testGetInstalledMigrationClasses()
    {
        $installedMigrationClasses = $this->migrationsLock->getOrderedInstalledMigrationClasses();

        $this->assertCount(2, $installedMigrationClasses);
        $this->assertContains(Version20180101000001::class, $installedMigrationClasses);
        $this->assertContains(Version20180101000003::class, $installedMigrationClasses);
    }

    public function testGetOrderedInstalledMigrationClasses()
    {
        $orderedMigrationClasses = $this->migrationsLock->getOrderedInstalledMigrationClasses();

        $migrationClassesByPositions = array_values($orderedMigrationClasses);
        $migrationPositionsByClasses = array_flip($migrationClassesByPositions);

        $this->assertGreaterThan(
            $migrationPositionsByClasses[Version20180101000003::class],
            $migrationPositionsByClasses[Version20180101000001::class]
        );
    }

    public function testSaveNewMigration()
    {
        $newMigrationVersion = $this->createMigrationVersionMock(Version20180101000004::class);

        $this->migrationsLock->saveNewMigrations([$newMigrationVersion]);

        $installedMigrationClasses = $this->createNewMigrationsLock()->getOrderedInstalledMigrationClasses();

        $this->assertCount(3, $installedMigrationClasses);
        $this->assertContains(Version20180101000004::class, $installedMigrationClasses);
    }

    public function testSaveAlreadyInstalledMigration()
    {
        $alreadyInstalledMigrationVersion = $this->createMigrationVersionMock(Version20180101000001::class);

        $this->migrationsLock->saveNewMigrations([$alreadyInstalledMigrationVersion]);

        $installedMigrationClasses = $this->createNewMigrationsLock()->getOrderedInstalledMigrationClasses();

        $this->assertCount(2, $installedMigrationClasses);
    }

    /**
     * @param string $className
     * @return \Doctrine\DBAL\Migrations\Version|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createMigrationVersionMock(string $className)
    {
        // Remove everything but the numbers at the end of the class name
        $version = preg_replace('~^.*?(\d+)$~', '$1', $className);

        $mock = $this->createMock(Version::class);

        $mock->method('getVersion')->willReturn($version);
        $mock->method('getMigration')->willReturn(new $className());

        return $mock;
    }
}
