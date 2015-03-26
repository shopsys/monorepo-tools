<?php

namespace SS6\ShopBundle\TestsPerformance;

use SS6\ShopBundle\TestsPerformance\PagePerformanceResultsCollection;

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
	public function getDurationFormatterTag($duration) {
		return 'fg=' . $this->getStatusConsoleTextColor($this->getDurationStatus($duration));
	}

	/**
	 * @param int $queryCount
	 * @return string
	 */
	public function getQueryCountFormatterTag($queryCount) {
		return 'fg=' . $this->getStatusConsoleTextColor($this->getQueryCountStatus($queryCount));
	}

	/**
	 * @param \SS6\ShopBundle\TestsPerformance\PagePerformanceResultsCollection $collection
	 * @return int
	 */
	public function getPagePerformanceCollectionStatus(PagePerformanceResultsCollection $collection) {
		$allStatuses = [self::STATUS_OK];

		foreach ($collection->getAll() as $pagePerformanceResult) {
			$allStatuses[] = $this->getDurationStatus($pagePerformanceResult->getAvgDuration());
			$allStatuses[] = $this->getQueryCountStatus($pagePerformanceResult->getMaxQueryCount());
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
	private function getDurationStatus($duration) {
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
	private function getQueryCountStatus($queryCount) {
		if ($queryCount >= self::QUERY_COUNT_CRITICAL) {
			return self::STATUS_CRITICAL;
		} elseif ($queryCount >= self::QUERY_COUNT_WARNING) {
			return self::STATUS_WARNING;
		}

		return self::STATUS_OK;
	}

}
