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
	 * @param string $routeName
	 * @param string $url
	 */
	public function __construct($routeName, $url) {
		$this->routeName = $routeName;
		$this->url = $url;
		$this->durations = [];
		$this->queryCounts = [];
	}

	/**
	 * @param float $duration
	 * @param int $queryCount
	 */
	public function addMeasurement($duration, $queryCount) {
		$this->durations[] = $duration;
		$this->queryCounts[] = $queryCount;
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

}
