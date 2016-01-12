<?php

namespace SS6\ShopBundle\Tests\Performance;

class PerformanceTestSummaryPrinter {

	/**
	 * @param \SS6\ShopBundle\Tests\Performance\PerformanceTestSample[] $performanceTestSamples
	 * @param \SS6\ShopBundle\Tests\Performance\ThresholdService $thresholdService
	 * @param \Symfony\Component\Console\Output\ConsoleOutput $consoleOutput
	 */
	public function printSummary(
		array $performanceTestSamples,
		ThresholdService $thresholdService,
		ConsoleOutput $consoleOutput
	) {
		foreach ($performanceTestSamples as $performanceTestSample) {
			$consoleOutput->writeln('');
			$consoleOutput->writeln(
				'Route name: ' . $performanceTestSample->getRouteName() . ' (' . $performanceTestSample->getUrl() . ')'
			);

			$tag = $thresholdService->getFormatterTagForDuration($performanceTestSample->getDuration());
			$consoleOutput->writeln(
				'<' . $tag . '>Average duration: ' . $performanceTestSample->getDuration() . 'ms</' . $tag . '>'
			);

			$tag = $thresholdService->getFormatterTagForQueryCount($performanceTestSample->getQueryCount());
			$consoleOutput->writeln(
				'<' . $tag . '>Max query count: ' . $performanceTestSample->getQueryCount() . '</' . $tag . '>'
			);

			if (!$performanceTestSample->isSuccessful()) {
				$tag = $thresholdService->getFormatterTagForError();
				$consoleOutput->writeln('<' . $tag . '>Wrong response status code</' . $tag . '>');
			}
		}

		$resultStatus = $thresholdService->getPerformanceTestSamplesStatus($performanceTestSamples);
		$resultColor = $thresholdService->getStatusConsoleTextColor($resultStatus);
		$resultTag = 'fg=' . $resultColor;
		$consoleOutput->writeln('');
		switch ($resultStatus) {
			case ThresholdService::STATUS_OK:
				$consoleOutput->write('<' . $resultTag . '>Test passed</' . $resultTag . '>');
				return;
			case ThresholdService::STATUS_WARNING:
				$consoleOutput->write('<' . $resultTag . '>Test passed, but contains some warnings</' . $resultTag . '>');
				return;
			case ThresholdService::STATUS_CRITICAL:
			default:
				$consoleOutput->write('<' . $resultTag . '>Test failed</' . $resultTag . '>');
				return;
		}
	}

}
