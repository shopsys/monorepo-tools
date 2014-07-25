<?php

namespace SS6\ShopBundle\Tests\Model\Order\Status;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderService;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusService;
use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException;
use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionWithOrdersException;
use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusException;

class OrderStatusServiceTest extends PHPUnit_Framework_TestCase {

	public function testDelete() {
		$orderService = new OrderService();
		$orderStatusService = new OrderStatusService($orderService);
		$orderStatus = new OrderStatus(
			'statusName',
			OrderStatus::TYPE_IN_PROGRESS
		);
		$orderStatusService->delete($orderStatus, array());
	}

	public function testDeleteForbiddenProvider() {
		return array(
			array('type' => OrderStatus::TYPE_NEW, 'expectedException' => OrderStatusDeletionForbiddenException::class),
			array('type' => OrderStatus::TYPE_IN_PROGRESS, 'expectedException' => null),
			array('type' => OrderStatus::TYPE_DONE, 'expectedException' => OrderStatusDeletionForbiddenException::class),
			array('type' => OrderStatus::TYPE_CANCELED, 'expectedException' => OrderStatusDeletionForbiddenException::class),
		);
	}

	/**
	 * @dataProvider testDeleteForbiddenProvider
	 */
	public function testDeleteForbidden($statusType, $expectedException = null) {
		$orderService = new OrderService();
		$orderStatusService = new OrderStatusService($orderService);
		$orderStatus = new OrderStatus(
			'statusName',
			$statusType
		);
		if ($expectedException !== null) {
			$this->setExpectedException($expectedException);
		}
		$orderStatusService->delete($orderStatus, array());
	}

	public function testDeleteWithOrder() {
		$orderService = new OrderService();
		$orderStatusService = new OrderStatusService($orderService);
		$orderStatus = new OrderStatus(
			'statusName',
			OrderStatus::TYPE_IN_PROGRESS
		);

		$orderMock = $this->getMockBuilder(Order::class)
			->setMethods(array('__construct'))
			->disableOriginalConstructor()
			->getMock();

		$this->setExpectedException(OrderStatusDeletionWithOrdersException::class);
		$orderStatusService->delete($orderStatus, array($orderMock));
	}

	public function testDeleteForbiddenOrWithOrder() {
		$orderService = new OrderService();
		$orderStatusService = new OrderStatusService($orderService);
		$orderStatus = new OrderStatus(
			'statusName',
			OrderStatus::TYPE_NEW
		);

		$orderMock = $this->getMockBuilder(Order::class)
			->setMethods(array('__construct'))
			->disableOriginalConstructor()
			->getMock();

		$this->setExpectedException(OrderStatusException::class);
		$orderStatusService->delete($orderStatus, array($orderMock));
	}

	public function testReplaceAndDelete() {
		$orderService = new OrderService();
		$orderStatusService = new OrderStatusService($orderService);
		$oldOrderStatus = new OrderStatus(
			'Old status',
			OrderStatus::TYPE_IN_PROGRESS
		);
		$newOrderStatus = new OrderStatus(
			'New status',
			OrderStatus::TYPE_IN_PROGRESS
		);

		$orderMock = $this->getMockBuilder(Order::class)
			->setMethods(array('__construct', 'setStatus'))
			->disableOriginalConstructor()
			->getMock();
		$orderMock->expects($this->once())->method('setStatus');

		$orderStatusService->delete($oldOrderStatus, array($orderMock), $newOrderStatus);
	}
	
}
