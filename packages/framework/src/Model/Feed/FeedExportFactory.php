<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

class FeedExportFactory
{
    const TEMPORARY_FILENAME_SUFFIX = '.tmp';
    const BATCH_SIZE = 1000;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedRendererFactory
     */
    protected $feedRendererFactory;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $filesystem;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedPathProvider
     */
    private $feedPathProvider;

    public function __construct(
        FeedRendererFactory $feedRendererFactory,
        FilesystemInterface $filesystem,
        EntityManagerInterface $em,
        FeedPathProvider $feedPathProvider
    ) {
        $this->feedRendererFactory = $feedRendererFactory;
        $this->filesystem = $filesystem;
        $this->em = $em;
        $this->feedPathProvider = $feedPathProvider;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInterface $feed
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param string|null $lastSeekId
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedExport
     */
    public function create(FeedInterface $feed, DomainConfig $domainConfig, string $lastSeekId = null): FeedExport
    {
        $feedRenderer = $this->feedRendererFactory->create($feed);
        $feedFilepath = $this->feedPathProvider->getFeedFilepath($feed->getInfo(), $domainConfig);

        return new FeedExport($feed, $domainConfig, $feedRenderer, $this->filesystem, $this->em, $feedFilepath, $lastSeekId);
    }
}
