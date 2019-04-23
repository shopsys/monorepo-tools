<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\String\TransformString;
use Symfony\Component\Filesystem\Filesystem;

class FeedExport
{
    const TEMPORARY_FILENAME_SUFFIX = '.tmp';
    const BATCH_SIZE = 1000;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedInterface
     */
    protected $feed;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    protected $domainConfig;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedRenderer
     */
    protected $feedRenderer;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $localFilesystem;

    /**
     * @var \League\Flysystem\MountManager
     */
    protected $mountManager;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $feedFilepath;

    /**
     * @var string
     */
    protected $feedLocalFilepath;

    /**
     * @var int|null
     */
    protected $lastSeekId;

    /**
     * @var string|null
     */
    protected $fileContentBuffer;

    /**
     * @var bool
     */
    protected $finished = false;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInterface $feed
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedRenderer $feedRenderer
     * @param \League\Flysystem\FilesystemInterface $filesystem
     * @param \Symfony\Component\Filesystem\Filesystem $localFilesystem
     * @param \League\Flysystem\MountManager $mountManager
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param string $feedFilepath
     * @param string $feedLocalFilepath
     * @param int|null $lastSeekId
     */
    public function __construct(
        FeedInterface $feed,
        DomainConfig $domainConfig,
        FeedRenderer $feedRenderer,
        FilesystemInterface $filesystem,
        Filesystem $localFilesystem,
        MountManager $mountManager,
        EntityManagerInterface $em,
        string $feedFilepath,
        string $feedLocalFilepath,
        ?int $lastSeekId
    ) {
        $this->feed = $feed;
        $this->domainConfig = $domainConfig;
        $this->feedRenderer = $feedRenderer;
        $this->filesystem = $filesystem;
        $this->localFilesystem = $localFilesystem;
        $this->mountManager = $mountManager;
        $this->em = $em;
        $this->feedFilepath = $feedFilepath;
        $this->feedLocalFilepath = $feedLocalFilepath;
        $this->lastSeekId = $lastSeekId;
    }

    public function wakeUp(): void
    {
        if ($this->filesystem->has($this->getTemporaryFilepath())) {
            $this->mountManager->move('main://' . $this->getTemporaryFilepath(), 'local://' . TransformString::removeDriveLetterFromPath($this->getTemporaryLocalFilepath()));
        } else {
            $this->localFilesystem->touch($this->getTemporaryLocalFilepath());
        }
    }

    public function sleep(): void
    {
        $this->mountManager->move('local://' . TransformString::removeDriveLetterFromPath($this->getTemporaryLocalFilepath()), 'main://' . $this->getTemporaryFilepath());
    }

    public function generateBatch(): void
    {
        if ($this->finished) {
            return;
        }

        $itemsInBatch = $this->feed->getItems($this->domainConfig, $this->lastSeekId, self::BATCH_SIZE);

        if ($this->lastSeekId === null) {
            $this->writeToFeed($this->feedRenderer->renderBegin($this->domainConfig));
        }

        $countInBatch = 0;
        foreach ($itemsInBatch as $item) {
            $this->writeToFeed($this->feedRenderer->renderItem($this->domainConfig, $item));
            $this->lastSeekId = $item->getSeekId();
            $countInBatch++;
        }

        if ($countInBatch < self::BATCH_SIZE) {
            $this->writeToFeed($this->feedRenderer->renderEnd($this->domainConfig));
            $this->finishFile();
        }

        $this->em->clear();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface
     */
    public function getFeedInfo(): FeedInfoInterface
    {
        return $this->feed->getInfo();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    public function getDomainConfig(): DomainConfig
    {
        return $this->domainConfig;
    }

    /**
     * @return int|null
     */
    public function getLastSeekId(): ?int
    {
        return $this->lastSeekId;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    protected function finishFile(): void
    {
        if ($this->filesystem->has($this->feedFilepath)) {
            $this->filesystem->delete($this->feedFilepath);
        }

        $this->mountManager->move('local://' . TransformString::removeDriveLetterFromPath($this->getTemporaryLocalFilepath()), 'main://' . $this->feedFilepath);

        $this->finished = true;
    }

    /**
     * @param string $content
     */
    protected function writeToFeed(string $content): void
    {
        $this->localFilesystem->appendToFile($this->getTemporaryLocalFilepath(), $content);
    }

    /**
     * @return string
     */
    protected function getTemporaryFilepath(): string
    {
        return $this->feedFilepath . self::TEMPORARY_FILENAME_SUFFIX;
    }

    /**
     * @return string
     */
    protected function getTemporaryLocalFilepath(): string
    {
        return $this->feedLocalFilepath . '_local' . self::TEMPORARY_FILENAME_SUFFIX;
    }
}
