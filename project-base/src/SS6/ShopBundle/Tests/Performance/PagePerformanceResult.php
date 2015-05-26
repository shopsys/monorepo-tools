<?php

namespace SS6\ShopBundle\Tests\Performance;

class PagePerformanceResult {

	/**
	 * @var string
	 */
	private $routeName;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var float[]
	 */
	private $durations;

	/**
	 * @var int[]
	 */
	private $queryCounts;

	/**
	 * @var int[]
	 */
	private $statusCodes;

	/**
	 * @var boolean[]
	 */
	private $isSuccessfulResults;

	/**
	 * @param string $routeName
	 * @param string $url
	 */
	public function __construct($routeName, $url) {
		$this->routeName = $routeName;
		$this->url = $url;
		$this->durations = [];
		$this->queryCounts = [];
		$this->statusCodes = [];
		$this->isSuccessfulResults = [];
	}

	/**
	 * @param float $duration
	 * @param int $queryCount
	 * @param int $statusCode
	 * @param boolean $isSuccessful
	 */
	public function addMeasurement($duration, $queryCount, $statusCode, $isSuccessful) {
		$this->durations[] = $duration;
		$this->queryCounts[] = $queryCount;
		$this->statusCodes[] = $statusCode;
		$this->isSuccessfulResults[] = $isSuccessful;
	}

	/**
	 * @return string
	 */
	public function getRouteName() {
		return $this->routeName;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @return int
	 */
	public function getAvgDuration() {
		return round(array_sum($this->durations) / count($this->durations));
	}

	/**
	 * @return int
	 */
	public function getMaxQueryCount() {
		return max($this->queryCounts);
	}

	/**
	 * @return int
	 */
	public function getMeasurementsCount() {
		return count($this->durations);
	}

	/**
	 * @return int
	 */
	public function getErrorsCount() {
		$successesCount = 0;

		foreach ($this->isSuccessfulResults as $isSuccessful) {
			if ($isSuccessful) {
				$successesCount++;
			}
		}

		return $this->getMeasurementsCount() - $successesCount;
	}

	/**
	 * @return int|null
	 */
	public function getMostImportantStatusCode() {
		$mostImportantStatusCode = null;

		foreach ($this->statusCodes as $key => $statusCode) {
			$mostImportantStatusCode = $statusCode;

			if (!$this->isSuccessfulResults[$key]) {
				return $mostImportantStatusCode;
			}
		}

		return $mostImportantStatusCode;
	}

}
