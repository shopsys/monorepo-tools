<?php

namespace Shopsys\MigrationBundle\Component\Doctrine\Migrations;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class MigrationsLocator
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $relativeDirectory;

    /**
     * @var string
     */
    private $relativeNamespace;

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param string $relativeDirectory
     * @param string $relativeNamespace
     */
    public function __construct(KernelInterface $kernel, Filesystem $filesystem, $relativeDirectory, $relativeNamespace)
    {
        $this->kernel = $kernel;
        $this->filesystem = $filesystem;
        $this->relativeDirectory = $relativeDirectory;
        $this->relativeNamespace = $relativeNamespace;
    }

    /**
     * Gets possible locations of migration classes to allow multiple sources of migrations.
     *
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
     * Creates a locations of migration classes for a particular bundle.
     *
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @return \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation
     */
    public function createMigrationsLocation(BundleInterface $bundle)
    {
        return new MigrationsLocation(
            $bundle->getPath() . '/' . $this->relativeDirectory,
            $bundle->getNamespace() . '\\' . $this->relativeNamespace
        );
    }
}
