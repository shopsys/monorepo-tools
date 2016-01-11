<?php

namespace SS6\ShopBundle\Tests\Performance;

class ThresholdService {

	const STATUS_CRITICAL = 2;
	const STATUS_WARNING = 1;
	const STATUS_OK = 0;

	const DURATION_CRITICAL = 3000;
	const DURATION_WARNING = 1000;

	const QUERY_COUNT_CRITICAL = 300;
	const QUERY_COUNT_WARNING = 100;

	/**
	 * @param float $duration
	 * @return string
	 */
	public function getFormatterTagForDuration($duration) {
		return 'fg=' . $this->getStatusConsoleTextColor($this->getStatusForDuration($duration));
	}

	/**
	 * @param int $queryCount
	 * @return string
	 */
	public function getFormatterTagForQueryCount($queryCount) {
		return 'fg=' . $this->getStatusConsoleTextColor($this->getStatusForQueryCount($queryCount));
	}

	/**
	 * @param int $errorsCount
	 * @return string
	 */
	public function getFormatterTagForErrorsCount($errorsCount) {
		return 'fg=' . $this->getStatusConsoleTextColor($this->getStatusForErrorsCount($errorsCount));
	}

	/**
	 * @param \SS6\ShopBundle\Tests\Performance\PerformanceTestSample[] $performanceTestSamples
	 * @return int
	 */
	public function getPerformanceTestSamplesStatus(array $performanceTestSamples) {
		$allStatuses = [self::STATUS_OK];

		foreach ($performanceTestSamples as $performanceTestSample) {
			$allStatuses[] = $this->getStatusForDuration($performanceTestSample->getDuration());
			$allStatuses[] = $this->getStatusForQueryCount($performanceTestSample->getQueryCount());
			$allStatuses[] = $this->getStatusForErrorsCount($performanceTestSample->isSuccessful() ? 0 : 1);
		}

		return max($allStatuses);
	}

	/**
	 * @param int $status
	 * @return string
	 */
	public function getStatusConsoleTextColor($status) {
		switch ($status) {
			case self::STATUS_OK:
				return 'green';
			case self::STATUS_WARNING:
				return 'yellow';
			default:
				return 'red';
		}
	}

	/**
	 * @param float $duration
	 * @return int
	 */
	private function getStatusForDuration($duration) {
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
	private function getStatusForQueryCount($queryCount) {
		if ($queryCount >= self::QUERY_COUNT_CRITICAL) {
			return self::STATUS_CRITICAL;
		} elseif ($queryCount >= self::QUERY_COUNT_WARNING) {
			return self::STATUS_WARNING;
		}

		return self::STATUS_OK;
	}

	/**
	 * @param int $errorsCount
	 * @return int
	 */
	public function getStatusForErrorsCount($errorsCount) {
		return $errorsCount > 0 ? self::STATUS_CRITICAL : self::STATUS_OK;
	}

}
