<?php

namespace Shopsys\ShopBundle\Tests\Performance\Page;

class PerformanceTestSample
{
    /**
     * @var string
     */
    private $routeName;

    /**
     * @var string
     */
    private $url;

    /**
     * @var float
     */
    private $duration;

    /**
     * @var int
     */
    private $queryCount;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var bool
     */
    private $isSuccessful;

    /**
     * @param string $routeName
     * @param string $url
     * @param float $duration
     * @param int $queryCount
     * @param int $statusCode
     * @param bool $isSuccessful
     */
    public function __construct(
        $routeName,
        $url,
        $duration,
        $queryCount,
        $statusCode,
        $isSuccessful
    ) {
        $this->routeName = $routeName;
        $this->url = $url;
        $this->duration = $duration;
        $this->queryCount = $queryCount;
        $this->statusCode = $statusCode;
        $this->isSuccessful = $isSuccessful;
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
    public function getQueryCount()
    {
        return $this->queryCount;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->isSuccessful;
    }
}
