<?php

namespace SS6\ShopBundle\Tests\Model\Order\Status;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;
use SS6\ShopBundle\Model\Order\Status\OrderStatusService;
use SS6\ShopBundle\Model\Order\Status\Exception\DeletionForbiddenOrderStatusException;

class OrderStatusServiceTest extends PHPUnit_Framework_TestCase {

	public function testDelete() {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus('statusName', OrderStatusRepository::STATUS_IN_PROGRESS);
		$this->assertNull($orderStatusService->delete($orderStatus));
	}

	public function testDeleteForbidden() {
		$orderStatusService = new OrderStatusService();
		$orderStatus = new OrderStatus('statusName', OrderStatusRepository::STATUS_NEW);
		$this->setExpectedException(DeletionForbiddenOrderStatusException::class);
		$orderStatusService->delete($orderStatus);
	}
	
}
