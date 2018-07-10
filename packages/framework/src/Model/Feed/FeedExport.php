<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

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
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $feedFilepath;

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

    public function __construct(
        FeedInterface $feed,
        DomainConfig $domainConfig,
        FeedRenderer $feedRenderer,
        FilesystemInterface $filesystem,
        EntityManagerInterface $em,
        string $feedFilepath,
        ?int $lastSeekId
    ) {
        $this->feed = $feed;
        $this->domainConfig = $domainConfig;
        $this->feedRenderer = $feedRenderer;
        $this->filesystem = $filesystem;
        $this->em = $em;
        $this->feedFilepath = $feedFilepath;
        $this->lastSeekId = $lastSeekId;
    }

    public function generateBatch(): void
    {
        if ($this->finished) {
            return;
        }

        $itemsInBatch = $this->feed->getItems($this->domainConfig, $this->lastSeekId, self::BATCH_SIZE);

        if ($this->lastSeekId === null) {
            $this->fileContentBuffer = $this->feedRenderer->renderBegin($this->domainConfig);
        } elseif ($this->fileContentBuffer === null) {
            $this->readFileToBuffer();
        }

        $countInBatch = 0;
        foreach ($itemsInBatch as $item) {
            $this->fileContentBuffer .= $this->feedRenderer->renderItem($this->domainConfig, $item);
            $this->lastSeekId = $item->getSeekId();
            $countInBatch++;
        }

        if ($countInBatch < self::BATCH_SIZE) {
            $this->fileContentBuffer .= $this->feedRenderer->renderEnd($this->domainConfig);
            $this->finishFile();
        } else {
            $this->writeBufferToFile();
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

    protected function readFileToBuffer(): void
    {
        if (!$this->filesystem->has($this->getTemporaryFilepath())) {
            $this->fileContentBuffer = '';
        }

        $this->fileContentBuffer = $this->filesystem->read($this->getTemporaryFilepath());
    }

    protected function writeBufferToFile(): void
    {
        $this->filesystem->put($this->getTemporaryFilepath(), $this->fileContentBuffer);
    }

    protected function finishFile(): void
    {
        $this->writeBufferToFile();

        if ($this->filesystem->has($this->feedFilepath)) {
            $this->filesystem->delete($this->feedFilepath);
        }
        $this->filesystem->rename($this->getTemporaryFilepath(), $this->feedFilepath);

        $this->finished = true;
    }

    /**
     * @return string
     */
    protected function getTemporaryFilepath(): string
    {
        return $this->feedFilepath . self::TEMPORARY_FILENAME_SUFFIX;
    }
}
