<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Order\Status;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException;
use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionWithOrdersException;
use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusException;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusData;
use SS6\ShopBundle\Model\Order\Status\OrderStatusService;

class OrderStatusServiceTest extends PHPUnit_Framework_TestCase {

	public function testDelete() {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus(
			new OrderStatusData(['en' => 'orderStatusName']),
			OrderStatus::TYPE_IN_PROGRESS
		);
		$orderStatusService->delete($orderStatus, []);
	}

	public function testDeleteForbiddenProvider() {
		return [
			['type' => OrderStatus::TYPE_NEW, 'expectedException' => OrderStatusDeletionForbiddenException::class],
			['type' => OrderStatus::TYPE_IN_PROGRESS, 'expectedException' => null],
			['type' => OrderStatus::TYPE_DONE, 'expectedException' => OrderStatusDeletionForbiddenException::class],
			['type' => OrderStatus::TYPE_CANCELED, 'expectedException' => OrderStatusDeletionForbiddenException::class],
		];
	}

	/**
	 * @dataProvider testDeleteForbiddenProvider
	 */
	public function testDeleteForbidden($statusType, $expectedException = null) {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus(
			new OrderStatusData(['en' => 'orderStatusName']),
			$statusType
		);
		if ($expectedException !== null) {
			$this->setExpectedException($expectedException);
		}
		$orderStatusService->delete($orderStatus, []);
	}

	public function testDeleteWithOrder() {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus(
			new OrderStatusData(['en' => 'orderStatusName']),
			OrderStatus::TYPE_IN_PROGRESS
		);

		$orderMock = $this->getMockBuilder(Order::class)
			->setMethods(['__construct'])
			->disableOriginalConstructor()
			->getMock();

		$this->setExpectedException(OrderStatusDeletionWithOrdersException::class);
		$orderStatusService->delete($orderStatus, [$orderMock]);
	}

	public function testDeleteForbiddenOrWithOrder() {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus(
			new OrderStatusData(['en' => 'orderStatusName']),
			OrderStatus::TYPE_NEW
		);

		$orderMock = $this->getMockBuilder(Order::class)
			->setMethods(['__construct'])
			->disableOriginalConstructor()
			->getMock();

		$this->setExpectedException(OrderStatusException::class);
		$orderStatusService->delete($orderStatus, [$orderMock]);
	}

	public function testReplaceAndDelete() {
		$orderStatusService = new OrderStatusService();
		$oldOrderStatus = new OrderStatus(
			new OrderStatusData(['en' => 'Old status']),
			OrderStatus::TYPE_IN_PROGRESS
		);
		$newOrderStatus = new OrderStatus(
			new OrderStatusData(['en' => 'New Status']),
			OrderStatus::TYPE_IN_PROGRESS
		);

		$orderMock = $this->getMockBuilder(Order::class)
			->setMethods(['__construct', 'setStatus'])
			->disableOriginalConstructor()
			->getMock();
		$orderMock->expects($this->once())->method('setStatus');

		$orderStatusService->delete($oldOrderStatus, [$orderMock], $newOrderStatus);
	}

}
