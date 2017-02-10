<?php

namespace Shopsys\ShopBundle\Tests\Performance\Page;

use Shopsys\ShopBundle\Tests\Performance\Page\PerformanceTestSample;

class PerformanceTestSampleQualifier {

    const STATUS_OK = 0;
    const STATUS_WARNING = 1;
    const STATUS_CRITICAL = 2;

    const DURATION_WARNING = 1000;
    const DURATION_CRITICAL = 3000;

    const QUERY_COUNT_WARNING = 100;
    const QUERY_COUNT_CRITICAL = 300;

    /**
     * @param float $duration
     * @return int
     */
    public function getStatusForDuration($duration) {
        if ($duration >= self::DURATION_CRITICAL) {
            return self::STATUS_CRITICAL;
        } elseif ($duration >= self::DURATION_WARNING) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }

    /**
     * @param int $queryCount
     * @return int
     */
    public function getStatusForQueryCount($queryCount) {
        if ($queryCount >= self::QUERY_COUNT_CRITICAL) {
            return self::STATUS_CRITICAL;
        } elseif ($queryCount >= self::QUERY_COUNT_WARNING) {
            return self::STATUS_WARNING;
        }

        return self::STATUS_OK;
    }

    /**
     * @param \Shopsys\ShopBundle\Tests\Performance\Page\PerformanceTestSample $performanceTestSample
     * @return int
     */
    public function getSampleStatus(PerformanceTestSample $performanceTestSample) {
        $overallStatus = self::STATUS_OK;

        if ($this->getStatusForDuration($performanceTestSample->getDuration()) > $overallStatus) {
            $overallStatus = $this->getStatusForDuration($performanceTestSample->getDuration());
        }

        if ($this->getStatusForQueryCount($performanceTestSample->getQueryCount()) > $overallStatus) {
            $overallStatus = $this->getStatusForQueryCount($performanceTestSample->getQueryCount());
        }

        if (!$performanceTestSample->isSuccessful()) {
            $overallStatus = self::STATUS_CRITICAL;
        }

        return $overallStatus;
    }

    /**
     * @param \Shopsys\ShopBundle\Tests\Performance\Page\PerformanceTestSample[] $performanceTestSamples
     * @return int
     */
    public function getOverallStatus(array $performanceTestSamples) {
        $allStatuses = [self::STATUS_OK];

        foreach ($performanceTestSamples as $performanceTestSample) {
            $allStatuses[] = $this->getSampleStatus($performanceTestSample);
        }

        return max($allStatuses);
    }

}
