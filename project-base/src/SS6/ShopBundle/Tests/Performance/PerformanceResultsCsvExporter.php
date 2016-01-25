<?php

namespace SS6\ShopBundle\Tests\Performance;

class PerformanceResultsCsvExporter {

	/**
	 * @param \SS6\ShopBundle\Tests\Performance\PerformanceTestSample[] $performanceTestSamples
	 * @param string $outputFilename
	 */
	public function exportJmeterCsvReport(
		array $performanceTestSamples,
		$outputFilename
	) {
		$handle = fopen($outputFilename, 'w');

		fputcsv($handle, [
			'timestamp',
			'elapsed',
			'label',
			'responseCode',
			'success',
			'URL',
			'Variables',
		]);

		foreach ($performanceTestSamples as $performanceTestSample) {
			fputcsv($handle, [
				time(),
				round($performanceTestSample->getDuration()),
				$performanceTestSample->getRouteName(),
				$performanceTestSample->getStatusCode(),
				$performanceTestSample->isSuccessful() ? 'true' : 'false',
				'/' . $performanceTestSample->getUrl(),
				$performanceTestSample->getQueryCount(),
			]);
		}

		fclose($handle);
	}

}
