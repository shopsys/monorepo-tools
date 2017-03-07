<?php

namespace Tests\ShopBundle\Performance\Page;

use Tests\ShopBundle\Performance\Page\PerformanceTestSample;

class PerformanceTestSamplesAggregator
{
    /**
     * @param \Tests\ShopBundle\Performance\Page\PerformanceTestSample[] $performanceTestSamples
     * @return \Tests\ShopBundle\Performance\Page\PerformanceTestSample[]
     */
    public function getPerformanceTestSamplesAggregatedByUrl(
        array $performanceTestSamples
    ) {
        $aggregatedPerformanceTestSamples = [];

        $performanceTestSamplesGroupedByUrl = $this->getPerformanceTestSamplesGroupedByUrl($performanceTestSamples);

        foreach ($performanceTestSamplesGroupedByUrl as $url => $performanceTestSamplesOfUrl) {
            $samplesCount = 0;
            $totalDuration = 0;
            $maxQueryCount = 0;
            $isSuccessful = true;
            $worstStatusCode = null;

            foreach ($performanceTestSamplesOfUrl as $performanceTestSample) {
                /* @var $performanceTestSample \Tests\ShopBundle\Performance\Page\PerformanceTestSample */

                $samplesCount++;
                $totalDuration += $performanceTestSample->getDuration();

                if ($performanceTestSample->getQueryCount() > $maxQueryCount) {
                    $maxQueryCount = $performanceTestSample->getQueryCount();
                }

                if (!$performanceTestSample->isSuccessful()) {
                    $isSuccessful = false;
                }

                if ($performanceTestSample->isSuccessful() || $worstStatusCode === null) {
                    $worstStatusCode = $performanceTestSample->getStatusCode();
                }
            }

            $aggregatedPerformanceTestSamples[$url] = new PerformanceTestSample(
                $performanceTestSample->getRouteName(),
                $url,
                $totalDuration / $samplesCount,
                $maxQueryCount,
                $worstStatusCode,
                $isSuccessful
            );
        }

        return $aggregatedPerformanceTestSamples;
    }

    /**
     * @param \Tests\ShopBundle\Performance\Page\PerformanceTestSample[][] $performanceTestSamples
     */
    private function getPerformanceTestSamplesGroupedByUrl(array $performanceTestSamples)
    {
        $performanceTestSamplesGroupedByUrl = [];

        foreach ($performanceTestSamples as $performanceTestSample) {
            $performanceTestSamplesGroupedByUrl[$performanceTestSample->getUrl()][] = $performanceTestSample;
        }

        return $performanceTestSamplesGroupedByUrl;
    }
}
