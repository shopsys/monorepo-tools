<?php

namespace Tests\ShopBundle\Performance\Page;

use Doctrine\DBAL\Logging\SQLLogger;

class PerformanceTestSampleQueryCounter implements SQLLogger
{
    /**
     * @var int
     */
    private $queryCount = 0;

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->queryCount++;
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
    }

    /**
     * @return int
     */
    public function getQueryCount()
    {
        return $this->queryCount;
    }
}
