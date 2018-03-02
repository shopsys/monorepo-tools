<?php

namespace Tests\ShopBundle\Performance\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ProductFeed\FeedConfigInterface;

class PerformanceTestSample
{
    /**
     * @var \Shopsys\ProductFeed\FeedConfigInterface
     */
    private $feedConfig;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    private $domainConfig;

    /**
     * @var string
     */
    private $generationUri;

    /**
     * @var float
     */
    private $duration;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string|null
     */
    private $message;

    /**
     * @var string[]
     */
    private $failMessages = [];

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param $generationUri
     * @param $duration
     * @param $statusCode
     */
    public function __construct(
        FeedConfigInterface $feedConfig,
        DomainConfig $domainConfig,
        $generationUri,
        $duration,
        $statusCode
    ) {
        $this->feedConfig = $feedConfig;
        $this->domainConfig = $domainConfig;
        $this->generationUri = $generationUri;
        $this->duration = $duration;
        $this->statusCode = $statusCode;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param string $failMessage
     */
    public function addFailMessage($failMessage)
    {
        $this->failMessages[] = $failMessage;
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface
     */
    public function getFeedConfig()
    {
        return $this->feedConfig;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    public function getDomainConfig()
    {
        return $this->domainConfig;
    }

    /**
     * @return string
     */
    public function getGenerationUri()
    {
        return $this->generationUri;
    }

    /**
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string[]
     */
    public function getFailMessages()
    {
        return $this->failMessages;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return count($this->failMessages) === 0;
    }
}
