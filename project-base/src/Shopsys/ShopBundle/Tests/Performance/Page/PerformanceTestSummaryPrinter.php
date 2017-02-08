<?php

namespace SS6\ShopBundle\Tests\Performance\Page;

use SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSampleQualifier;
use Symfony\Component\Console\Output\ConsoleOutput;

class PerformanceTestSummaryPrinter {

	/**
	 * @var \SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSampleQualifier
	 */
	private $performanceTestSampleQualifier;

	public function __construct(PerformanceTestSampleQualifier $performanceTestSampleQualifier) {
		$this->performanceTestSampleQualifier = $performanceTestSampleQualifier;
	}

	/**
	 * @param \SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSample[] $performanceTestSamples
	 * @param \Symfony\Component\Console\Output\ConsoleOutput $consoleOutput
	 */
	public function printSummary(
		array $performanceTestSamples,
		ConsoleOutput $consoleOutput
	) {
		foreach ($performanceTestSamples as $performanceTestSample) {
			$sampleStatus = $this->performanceTestSampleQualifier->getSampleStatus($performanceTestSample);

			if ($sampleStatus !== PerformanceTestSampleQualifier::STATUS_OK) {
				$this->printSample($performanceTestSample, $consoleOutput);
			}
		}

		$resultStatus = $this->performanceTestSampleQualifier->getOverallStatus($performanceTestSamples);
		$resultColor = $this->getStatusConsoleTextColor($resultStatus);
		$resultTag = 'fg=' . $resultColor;
		$consoleOutput->writeln('');
		switch ($resultStatus) {
			case PerformanceTestSampleQualifier::STATUS_OK:
				$consoleOutput->write('<' . $resultTag . '>Test passed</' . $resultTag . '>');
				return;
			case PerformanceTestSampleQualifier::STATUS_WARNING:
				$consoleOutput->write('<' . $resultTag . '>Test passed, but contains some warnings</' . $resultTag . '>');
				return;
			case PerformanceTestSampleQualifier::STATUS_CRITICAL:
			default:
				$consoleOutput->write('<' . $resultTag . '>Test failed</' . $resultTag . '>');
				return;
		}
	}

	/**
	 * @param \SS6\ShopBundle\Tests\Performance\Page\PerformanceTestSample $performanceTestSample
	 * @param \Symfony\Component\Console\Output\ConsoleOutput $consoleOutput
	 */
	private function printSample(
		PerformanceTestSample $performanceTestSample,
		ConsoleOutput $consoleOutput
	) {
		$consoleOutput->writeln('');
		$consoleOutput->writeln(
			'Route name: ' . $performanceTestSample->getRouteName() . ' (' . $performanceTestSample->getUrl() . ')'
		);

		$tag = $this->getFormatterTagForDuration($performanceTestSample->getDuration());
		$consoleOutput->writeln(
			'<' . $tag . '>Average duration: ' . round($performanceTestSample->getDuration()) . 'ms</' . $tag . '>'
		);

		$tag = $this->getFormatterTagForQueryCount($performanceTestSample->getQueryCount());
		$consoleOutput->writeln(
			'<' . $tag . '>Max query count: ' . $performanceTestSample->getQueryCount() . '</' . $tag . '>'
		);

		if (!$performanceTestSample->isSuccessful()) {
			$tag = $this->getFormatterTagForError();
			$consoleOutput->writeln('<' . $tag . '>Wrong response status code</' . $tag . '>');
		}
	}

	/**
	 * @param float $duration
	 * @return string
	 */
	private function getFormatterTagForDuration($duration) {
		$status = $this->performanceTestSampleQualifier->getStatusForDuration($duration);
		return 'fg=' . $this->getStatusConsoleTextColor($status);
	}

	/**
	 * @param int $queryCount
	 * @return string
	 */
	private function getFormatterTagForQueryCount($queryCount) {
		$status = $this->performanceTestSampleQualifier->getStatusForQueryCount($queryCount);
		return 'fg=' . $this->getStatusConsoleTextColor($status);
	}

	/**
	 * @return string
	 */
	private function getFormatterTagForError() {
		return 'fg=' . $this->getStatusConsoleTextColor(PerformanceTestSampleQualifier::STATUS_CRITICAL);
	}

	/**
	 * @param int $status
	 * @return string
	 */
	private function getStatusConsoleTextColor($status) {
		switch ($status) {
			case PerformanceTestSampleQualifier::STATUS_OK:
				return 'green';
			case PerformanceTestSampleQualifier::STATUS_WARNING:
				return 'yellow';
			default:
				return 'red';
		}
	}

}
