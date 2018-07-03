<?php

namespace Shopsys\FrameworkBundle\Model\Sitemap;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Presta\SitemapBundle\DependencyInjection\Configuration;
use Presta\SitemapBundle\Service\Dumper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class SitemapDumper extends Dumper
{
    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $abstractFilesystem;

    /**
     * @var \League\Flysystem\MountManager
     */
    private $mountManager;

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
            $this->mountManager->move('local://' . $file->getPathname(), 'main://' . $targetDir . '/' . $file->getBasename());
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
