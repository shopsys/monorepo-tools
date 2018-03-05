<?php

namespace Tests\ShopBundle\Unit\Tests\Performance\Page;

use PHPUnit\Framework\TestCase;
use Tests\ShopBundle\Performance\JmeterCsvReporter;
use Tests\ShopBundle\Performance\Page\PerformanceResultsCsvExporter;
use Tests\ShopBundle\Performance\Page\PerformanceTestSample;

class PerformanceResultsCsvExporterTest extends TestCase
{
    public function testExportJmeterCsvReportWritesExpectedHeader()
    {
        $outputFilename = $this->getTemporaryFilename();

        $performanceResultsCsvExporter = $this->createPerformanceResultsCsvExporter();

        $performanceResultsCsvExporter->exportJmeterCsvReport(
            $this->getPerformanceTestSamples(),
            $outputFilename
        );

        $expectedLine = [
            'timestamp',
            'elapsed',
            'label',
            'responseCode',
            'success',
            'URL',
            'Variables',
        ];

        $this->assertCsvRowEquals($expectedLine, $outputFilename, 0);
    }

    public function testExportJmeterCsvReportRoundsDuration()
    {
        $outputFilename = $this->getTemporaryFilename();

        $performanceResultsCsvExporter = $this->createPerformanceResultsCsvExporter();

        $performanceResultsCsvExporter->exportJmeterCsvReport(
            $this->getPerformanceTestSamples(),
            $outputFilename
        );

        $line = $this->getCsvLine($outputFilename, 1);

        $this->assertEquals(1000, $line[1]);
    }

    /**
     * @return string
     */
    private function getTemporaryFilename()
    {
        return tempnam(sys_get_temp_dir(), 'test');
    }

    /**
     * @return \Tests\ShopBundle\Performance\Page\PerformanceTestSample[]
     */
    private function getPerformanceTestSamples()
    {
        $performanceTestSamples = [];

        $performanceTestSamples[] = new PerformanceTestSample(
            'routeName1',
            'url1',
            1000.1,
            10,
            200,
            true
        );
        $performanceTestSamples[] = new PerformanceTestSample(
            'routeName2',
            'url2',
            2000,
            20,
            301,
            true
        );

        return $performanceTestSamples;
    }

    /**
     * @param array $expectedLine
     * @param string $filename
     * @param int $lineIndex
     */
    private function assertCsvRowEquals(array $expectedLine, $filename, $lineIndex)
    {
        $actualLine = $this->getCsvLine($filename, $lineIndex);

        $this->assertSame($expectedLine, $actualLine);
    }

    /**
     * @param string $filename
     * @param int $lineIndex
     * @return array
     */
    private function getCsvLine($filename, $lineIndex)
    {
        $handle = fopen($filename, 'r');

        // seek to $rowIndex
        for ($i = 0; $i < $lineIndex; $i++) {
            fgetcsv($handle);
        }

        return fgetcsv($handle);
    }

    /**
     * @return \Tests\ShopBundle\Performance\Page\PerformanceResultsCsvExporter
     */
    private function createPerformanceResultsCsvExporter()
    {
        return new PerformanceResultsCsvExporter(new JmeterCsvReporter());
    }
}
