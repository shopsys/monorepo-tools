<?php

namespace Shopsys\FrameworkBundle\Model\Sitemap;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

class SitemapDumperFactory
{
    const MAX_ITEMS_IN_FILE = 50000;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $localFilesystem;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Sitemap\SitemapService
     */
    private $sitemapService;

    /**
     * @var \League\Flysystem\MountManager
     */
    private $mountManager;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $filesystem;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Filesystem $localFilesystem,
        FilesystemInterface $filesystem,
        MountManager $mountManager,
        SitemapService $sitemapService
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->localFilesystem = $localFilesystem;
        $this->sitemapService = $sitemapService;
        $this->mountManager = $mountManager;
        $this->filesystem = $filesystem;
    }

    /**
     * @param $domainId
     * @return \Shopsys\FrameworkBundle\Model\Sitemap\SitemapDumper
     */
    public function createForDomain($domainId)
    {
        return new SitemapDumper(
            $this->eventDispatcher,
            $this->localFilesystem,
            $this->filesystem,
            $this->mountManager,
            $this->sitemapService->getSitemapFilePrefixForDomain($domainId),
            self::MAX_ITEMS_IN_FILE
        );
    }
}
