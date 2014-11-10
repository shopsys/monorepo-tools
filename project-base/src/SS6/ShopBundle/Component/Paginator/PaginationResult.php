<?php

namespace SS6\ShopBundle\Component\Paginator;

class PaginationResult {

	/**
	 * @var int
	 */
	private $page;

	/**
	 * @var int
	 */
	private $limit;

	/**
	 * @var int
	 */
	private $totalCount;

	/**
	 * @var array
	 */
	private $results;

	/**
	 * @param int $page
	 * @param int $limit
	 * @param int $totalCount
	 * @param array $results
	 */
	public function __construct($page, $limit, $totalCount, $results) {
		$this->page = $page;
		$this->limit = $limit;
		$this->totalCount = $totalCount;
		$this->results = $results;
	}

	/**
	 * @return int
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @return int
	 */
	public function getTotalCount() {
		return $this->totalCount;
	}

	/**
	 * @return array
	 */
	public function getResults() {
		return $this->results;
	}


}
