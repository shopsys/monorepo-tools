<?php

namespace SS6\ShopBundle\Tests\Model\Order\Status;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;
use SS6\ShopBundle\Model\Order\Status\OrderStatusService;
use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException;
use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionWithOrdersException;
use SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusException;

class OrderStatusServiceTest extends PHPUnit_Framework_TestCase {

	public function testDelete() {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus('statusName', OrderStatusRepository::STATUS_IN_PROGRESS);
		$this->assertNull($orderStatusService->delete($orderStatus, 0));
	}

	public function testDeleteForbidden() {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus('statusName', OrderStatusRepository::STATUS_NEW);
		$this->setExpectedException(OrderStatusDeletionForbiddenException::class);
		$orderStatusService->delete($orderStatus, 0);
	}

	public function testDeleteWithOrder() {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus('statusName', OrderStatusRepository::STATUS_IN_PROGRESS);
		$this->setExpectedException(OrderStatusDeletionWithOrdersException::class);
		$orderStatusService->delete($orderStatus, 1);
	}

	public function testDeleteForbiddenOrWithOrder() {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus('statusName', OrderStatusRepository::STATUS_NEW);
		$this->setExpectedException(OrderStatusException::class);
		$orderStatusService->delete($orderStatus, 1);
	}
	
}
