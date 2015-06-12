<?php

namespace SS6\ShopBundle\Tests\Performance;

use SS6\ShopBundle\Tests\Performance\PagePerformanceResult;

class PagePerformanceResultsCollection {

	/**
	 * @var \SS6\ShopBundle\Tests\Performance\PagePerformanceResult[]
	 */
	private $results;

	public function __construct() {
		$this->results = [];
	}

	/**
	 * @param string $routeName
	 * @param string $url
	 * @param float $duration
	 * @param int $queryCount
	 * @param int $statusCode
	 * @param bool $isSuccessful
	 */
	public function addMeasurement($routeName, $url, $duration, $queryCount, $statusCode, $isSuccessful) {
		if (!array_key_exists($routeName, $this->results)) {
			$this->results[$routeName] = new PagePerformanceResult($routeName, $url);
		}
		$this->results[$routeName]->addMeasurement($duration, $queryCount, $statusCode, $isSuccessful);
	}

	/**
	 * @return \SS6\ShopBundle\Tests\Performance\PagePerformanceResult[]
	 */
	public function getAll() {
		return $this->results;
	}

	public function clear() {
		$this->results = [];
	}
}
