<?php

namespace SS6\ShopBundle\Tests\Performance;

class PerformanceResultsCsvExporter {

	/**
	 * @param \SS6\ShopBundle\Tests\Performance\PagePerformanceResultsCollection $pagePerformanceResultsCollection
	 * @param string $outputFilename
	 */
	public function exportJmeterCsvReport(
		PagePerformanceResultsCollection $pagePerformanceResultsCollection,
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
			'SampleCount',
			'ErrorCount',
			'Variables',
		]);

		foreach ($pagePerformanceResultsCollection->getAll() as $pagePerformanceResult) {
			fputcsv($handle, [
				time(),
				$pagePerformanceResult->getAvgDuration(),
				$pagePerformanceResult->getRouteName(),
				$pagePerformanceResult->getMostImportantStatusCode(),
				($pagePerformanceResult->getErrorsCount() === 0) ? 'true' : 'false',
				'/' . $pagePerformanceResult->getUrl(),
				$pagePerformanceResult->getMeasurementsCount(),
				$pagePerformanceResult->getErrorsCount(),
				$pagePerformanceResult->getMaxQueryCount(),
			]);
		}

		fclose($handle);
	}

}
