<?php

namespace Shopsys\FrameworkBundle\Model\Sitemap;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Presta\SitemapBundle\DependencyInjection\Configuration;
use Presta\SitemapBundle\Service\Dumper;
use Shopsys\FrameworkBundle\Component\String\TransformString;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class SitemapDumper extends Dumper
{
    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $abstractFilesystem;

    /**
     * @var \League\Flysystem\MountManager
     */
    protected $mountManager;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \League\Flysystem\FilesystemInterface $abstractFilesystem
     * @param \League\Flysystem\MountManager $mountManager
     * @param mixed $sitemapFilePrefix
     * @param mixed|null $itemsBySet
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        Filesystem $filesystem,
        FilesystemInterface $abstractFilesystem,
        MountManager $mountManager,
        $sitemapFilePrefix = Configuration::DEFAULT_FILENAME,
        $itemsBySet = null
    ) {
        parent::__construct($dispatcher, $filesystem, $sitemapFilePrefix, $itemsBySet);

        $this->abstractFilesystem = $abstractFilesystem;
        $this->mountManager = $mountManager;
    }

    /**
     * Moves sitemaps created in a temporary folder to their real location
     *
     * @param string $targetDir Directory to move created sitemaps to
     *
     * @throws \RuntimeException
     */
    protected function activate($targetDir)
    {
        $this->deleteExistingSitemaps($targetDir);

        $finder = new Finder();
        foreach ($finder->files()->in($this->tmpFolder)->getIterator() as $file) {
            $this->mountManager->move(
                'local://' . TransformString::removeDriveLetterFromPath($file->getPathname()),
                'main://' . $targetDir . '/' . $file->getBasename()
            );
        }

        parent::cleanup();
    }

    /**
     * Deletes sitemap files matching filename patterns of newly generated files
     *
     * @param string $targetDir
     */
    protected function deleteExistingSitemaps($targetDir)
    {
        $files = $this->abstractFilesystem->listContents($targetDir);
        foreach ($files as $file) {
            $this->abstractFilesystem->delete($file['path']);
        }
    }
}
