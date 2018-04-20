<?php

namespace Tests\MigrationBundle\Unit\Component\Doctrine\Migrations;

use PHPUnit\Framework\TestCase;
use Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class MigrationsLocatorTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $kernelMock;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem|\PHPUnit\Framework\MockObject\MockObject
     */
    private $filesystemMock;

    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator
     */
    private $migrationsLocator;

    protected function setUp()
    {
        $this->kernelMock = $this->createMock(KernelInterface::class);
        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->migrationsLocator = new MigrationsLocator($this->kernelMock, $this->filesystemMock);
    }

    public function testExistingMigrationsLocation()
    {
        $this->kernelReturnsOneBundle('Test\\MockBundle', 'test/MockBundle');
        $this->filesystemSaysPathExists('test/MockBundle/Migrations');

        $migrationsLocations = $this->migrationsLocator->getMigrationsLocations();

        $this->assertCount(1, $migrationsLocations);
    }

    public function testNonExistingMigrationsLocation()
    {
        $this->kernelReturnsOneBundle('Test\\MockBundle', 'test/MockBundle');
        $this->filesystemSaysPathExists('test/MockBundle/Migrations', false);

        $migrationsLocations = $this->migrationsLocator->getMigrationsLocations();

        $this->assertEmpty($migrationsLocations);
    }

    public function testMultipleMigrationsLocations()
    {
        $bundle = $this->createBundleMock('Test\\MockBundle', 'test/MockBundle');
        $this->kernelMock->method('getBundles')->willReturn([$bundle, $bundle, $bundle]);
        $this->filesystemSaysEveryPathExists();

        $migrationsLocations = $this->migrationsLocator->getMigrationsLocations();

        $this->assertCount(3, $migrationsLocations);
    }

    public function testMigrationsLocationParameters()
    {
        $this->kernelReturnsOneBundle('Test\\MockBundle', 'test/MockBundle');
        $this->filesystemSaysEveryPathExists();

        list($migrationsLocation) = $this->migrationsLocator->getMigrationsLocations();

        $this->assertEquals('Test\\MockBundle\\Migrations', $migrationsLocation->getNamespace());
        $this->assertEquals('test/MockBundle/Migrations', $migrationsLocation->getDirectory());
    }

    /**
     * @param string $namespace
     * @param string $path
     */
    private function kernelReturnsOneBundle($namespace, $path)
    {
        $this->kernelMock->method('getBundles')
            ->willReturn([$this->createBundleMock($namespace, $path)]);
    }

    /**
     * @param string $path
     * @param bool $exists
     */
    private function filesystemSaysPathExists($path, $exists = true)
    {
        $this->filesystemMock->method('exists')
            ->with($path)
            ->willReturn($exists);
    }

    /**
     * @param bool $exists
     */
    private function filesystemSaysEveryPathExists($exists = true)
    {
        $this->filesystemMock->method('exists')
            ->willReturn($exists);
    }

    /**
     * @param string $namespace
     * @param string $path
     * @return \Symfony\Component\HttpKernel\Bundle\BundleInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createBundleMock($namespace, $path)
    {
        $bundleMock = $this->createMock(BundleInterface::class);

        $bundleMock->method('getNamespace')
            ->willReturn($namespace);
        $bundleMock->method('getPath')
            ->willReturn($path);

        return $bundleMock;
    }
}
