<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Symfony\Component\Filesystem\Filesystem;

class FeedExportFactory
{
    /** @access protected */
    const TEMPORARY_FILENAME_SUFFIX = '.tmp';
    /** @access protected */
    const BATCH_SIZE = 1000;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedRendererFactory
     */
    protected $feedRendererFactory;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedPathProvider
     */
    protected $feedPathProvider;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $localFilesystem;

    /**
     * @var \League\Flysystem\MountManager
     */
    protected $mountManager;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedRendererFactory $feedRendererFactory
     * @param \League\Flysystem\FilesystemInterface $filesystem
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedPathProvider $feedPathProvider
     * @param \Symfony\Component\Filesystem\Filesystem $localFilesystem
     * @param \League\Flysystem\MountManager $mountManager
     */
    public function __construct(
        FeedRendererFactory $feedRendererFactory,
        FilesystemInterface $filesystem,
        EntityManagerInterface $em,
        FeedPathProvider $feedPathProvider,
        Filesystem $localFilesystem,
        MountManager $mountManager
    ) {
        $this->feedRendererFactory = $feedRendererFactory;
        $this->filesystem = $filesystem;
        $this->em = $em;
        $this->feedPathProvider = $feedPathProvider;
        $this->localFilesystem = $localFilesystem;
        $this->mountManager = $mountManager;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInterface $feed
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param string|null $lastSeekId
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedExport
     */
    public function create(FeedInterface $feed, DomainConfig $domainConfig, ?string $lastSeekId = null): FeedExport
    {
        $feedRenderer = $this->feedRendererFactory->create($feed);
        $feedFilepath = $this->feedPathProvider->getFeedFilepath($feed->getInfo(), $domainConfig);
        $feedLocalFilepath = $this->feedPathProvider->getFeedLocalFilepath($feed->getInfo(), $domainConfig);

        return new FeedExport(
            $feed,
            $domainConfig,
            $feedRenderer,
            $this->filesystem,
            $this->localFilesystem,
            $this->mountManager,
            $this->em,
            $feedFilepath,
            $feedLocalFilepath,
            $lastSeekId
        );
    }
}
