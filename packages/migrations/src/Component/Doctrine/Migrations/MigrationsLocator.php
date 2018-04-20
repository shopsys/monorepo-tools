<?php

namespace Shopsys\MigrationBundle\Component\Doctrine\Migrations;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class MigrationsLocator
{
    const MIGRATIONS_DIRECTORY = 'Migrations';
    const MIGRATIONS_NAMESPACE = 'Migrations';

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(KernelInterface $kernel, Filesystem $filesystem)
    {
        $this->kernel = $kernel;
        $this->filesystem = $filesystem;
    }

    /**
     * @return \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation[]
     */
    public function getMigrationsLocations()
    {
        $migrationsLocations = [];
        foreach ($this->kernel->getBundles() as $bundle) {
            $migrationsLocation = $this->createMigrationsLocation($bundle);
            if ($this->filesystem->exists($migrationsLocation->getDirectory())) {
                $migrationsLocations[] = $migrationsLocation;
            }
        }

        return $migrationsLocations;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @return \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation
     */
    public function createMigrationsLocation(BundleInterface $bundle)
    {
        return new MigrationsLocation(
            $bundle->getPath() . '/' . self::MIGRATIONS_DIRECTORY,
            $bundle->getNamespace() . '\\' . self::MIGRATIONS_NAMESPACE
        );
    }
}
