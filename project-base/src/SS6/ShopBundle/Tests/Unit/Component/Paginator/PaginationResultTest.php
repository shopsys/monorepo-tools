<?php

namespace SS6\ShopBundle\Tests\Unit\Component\Paginator;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Paginator\PaginationResult;

class PaginationResultTest extends PHPUnit_Framework_TestCase {

	public function getTestPageCountData() {
		return [
			[1, 10, 40, [], 4],
			[1, 10, 41, [], 5],
			[1, 10, 49, [], 5],
			[1, 10, 50, [], 5],
			[1, 10, 51, [], 6],
			[1, 10, 5, [], 1],
			[1, 0, 0, [], 0],
			[1, null, 5, [], 1],
			[1, null, 0, [], 0],
		];
	}

	/**
	 * @dataProvider getTestPageCountData
	 */
	public function testGetPageCount($page, $pageSize, $totalCount, $results, $expectedPageCount) {
		$paginationResult = new PaginationResult($page, $pageSize, $totalCount, $results);

		$this->assertSame($expectedPageCount, $paginationResult->getPageCount());
	}

}
