<?php

namespace SS6\ShopBundle\Tests\Unit\Tests\Performance;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Tests\Performance\PagePerformanceResultsCollection;
use SS6\ShopBundle\Tests\Performance\PerformanceResultsCsvExporter;

class PerformanceResultsCsvExporterTest extends PHPUnit_Framework_TestCase {

	public function testExportJmeterCsvReportWritesExpectedHeader() {
		$outputFilename = $this->getTemporaryFilename();

		$performanceResultsCsvExporter = new PerformanceResultsCsvExporter();

		$performanceResultsCsvExporter->exportJmeterCsvReport(
			$this->getPagePerformanceResultsCollection(),
			$outputFilename
		);

		$expectedLine = [
			'timestamp',
			'elapsed',
			'label',
			'responseCode',
			'success',
			'URL',
			'SampleCount',
			'ErrorCount',
			'Variables',
		];

		$this->assertCsvRowEquals($expectedLine, $outputFilename, 0);
	}

	/**
	 * @return string
	 */
	private function getTemporaryFilename() {
		return tempnam(sys_get_temp_dir(), 'test');
	}

	/**
	 * @return \SS6\ShopBundle\Tests\Performance\PagePerformanceResultsCollection
	 */
	private function getPagePerformanceResultsCollection() {
		$pagePerformanceResultsCollection = new PagePerformanceResultsCollection();
		$pagePerformanceResultsCollection->addMeasurement(
			'routeName1',
			'url1',
			1000,
			10,
			200,
			true
		);
		$pagePerformanceResultsCollection->addMeasurement(
			'routeName2',
			'url2',
			1000,
			20,
			301,
			true
		);

		return $pagePerformanceResultsCollection;
	}

	/**
	 * @param array $expectedLine
	 * @param string $filename
	 * @param int $lineIndex
	 */
	private function assertCsvRowEquals(array $expectedLine, $filename, $lineIndex) {
		$handle = fopen($filename, 'r');

		// seek to $rowIndex
		for ($i = 0; $i < $lineIndex; $i++) {
			fgetcsv($handle);
		}

		$actualLine = fgetcsv($handle);

		$this->assertSame($expectedLine, $actualLine);
	}

}
