<?php

namespace SS6\ShopBundle\Tests\Database\Model\Order\Status;

use SS6\ShopBundle\DataFixtures\Base\OrderStatusDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\OrderDataFixture;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderFacade;
use SS6\ShopBundle\Model\Order\Status\OrderStatusData;
use SS6\ShopBundle\Model\Order\Status\OrderStatusFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class OrderStatusFacadeTest extends DatabaseTestCase {

	public function testDeleteByIdAndReplace() {
		$em = $this->getEntityManager();
		$orderStatusFacade = $this->getContainer()->get(OrderStatusFacade::class);
		/* @var $orderStatusFacade \SS6\ShopBundle\Model\Order\Status\OrderStatusFacade */
		$orderFacade = $this->getContainer()->get(OrderFacade::class);
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */

		$orderStatusToDelete = $orderStatusFacade->create(new OrderStatusData(['cs' => 'name']));
		$orderStatusToReplaceWith = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
		/* @var $orderStatusToReplaceWith \SS6\ShopBundle\Model\Order\Status\OrderStatus */
		$order = $this->getReference(OrderDataFixture::ORDER_PREFIX . '1');
		/* @var $order \SS6\ShopBundle\Model\Order\Order */

		$orderData = new OrderData();
		$orderData->setFromEntity($order);
		$orderData->status = $orderStatusToDelete;
		$orderFacade->edit($order->getId(), $orderData);

		$orderStatusFacade->deleteById($orderStatusToDelete->getId(), $orderStatusToReplaceWith->getId());

		$em->refresh($order);

		$this->assertEquals($orderStatusToReplaceWith, $order->getStatus());
	}
}
