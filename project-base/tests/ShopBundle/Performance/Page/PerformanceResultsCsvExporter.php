<?php

namespace Tests\ShopBundle\Performance\Page;

use Tests\ShopBundle\Performance\JmeterCsvReporter;

class PerformanceResultsCsvExporter
{
    /**
     * @var \Tests\ShopBundle\Performance\JmeterCsvReporter
     */
    private $jmeterCsvReporter;

    public function __construct(JmeterCsvReporter $jmeterCsvReporter)
    {
        $this->jmeterCsvReporter = $jmeterCsvReporter;
    }

    /**
     * @param \Tests\ShopBundle\Performance\Page\PerformanceTestSample[] $performanceTestSamples
     * @param string $outputFilename
     */
    public function exportJmeterCsvReport(
        array $performanceTestSamples,
        $outputFilename
    ) {
        $handle = fopen($outputFilename, 'w');

        $this->jmeterCsvReporter->writeHeader($handle);

        foreach ($performanceTestSamples as $performanceTestSample) {
            $this->jmeterCsvReporter->writeLine(
                $handle,
                $performanceTestSample->getDuration(),
                $performanceTestSample->getRouteName(),
                $performanceTestSample->getStatusCode(),
                $performanceTestSample->isSuccessful(),
                $performanceTestSample->getUrl(),
                $performanceTestSample->getQueryCount()
            );
        }

        fclose($handle);
    }
}
