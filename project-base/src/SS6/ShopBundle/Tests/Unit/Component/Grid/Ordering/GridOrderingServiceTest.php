<?php

namespace SS6\ShopBundle\Component\Grid\Ordering;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Component\Grid\Ordering\GridOrderingService;
use SS6\ShopBundle\Component\Grid\Ordering\OrderableEntityInterface;

/**
 * @UglyTest
 */
class GridOrderingServiceTest extends PHPUnit_Framework_TestCase {

	public function testSetPositionNull() {
		$gridOrderingService = new GridOrderingService();
		$entity = null;

		$this->setExpectedException(\SS6\ShopBundle\Component\Grid\Ordering\Exception\EntityIsNotOrderableException::class);
		$gridOrderingService->setPosition($entity, 0);
	}

	public function testSetPositionWrongEntity() {
		$gridOrderingService = new GridOrderingService();
		$entity = new \StdClass();

		$this->setExpectedException(\SS6\ShopBundle\Component\Grid\Ordering\Exception\EntityIsNotOrderableException::class);
		$gridOrderingService->setPosition($entity, 0);
	}

	public function testSetPosition() {
		$gridOrderingService = new GridOrderingService();
		$position = 1;
		$entityMock = $this->getMockBuilder(OrderableEntityInterface::class)
			->setMethods(['setPosition'])
			->getMockForAbstractClass();
		$entityMock->expects($this->once())->method('setPosition')->with($this->equalTo($position));

		$gridOrderingService->setPosition($entityMock, $position);
	}

}
