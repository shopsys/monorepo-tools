<?php

namespace Shopsys\ShopBundle\Tests\Performance\Page;

use Shopsys\ShopBundle\Tests\Performance\JmeterCsvReporter;

class PerformanceResultsCsvExporter
{

    /**
     * @var \Shopsys\ShopBundle\Tests\Performance\JmeterCsvReporter
     */
    private $jmeterCsvReporter;

    public function __construct(JmeterCsvReporter $jmeterCsvReporter) {
        $this->jmeterCsvReporter = $jmeterCsvReporter;
    }

    /**
     * @param \Shopsys\ShopBundle\Tests\Performance\Page\PerformanceTestSample[] $performanceTestSamples
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
