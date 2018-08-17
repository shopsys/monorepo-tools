<?php

namespace Tests\ShopBundle\Database\Model\Order\Status;

use Shopsys\FrameworkBundle\DataFixtures\Demo\OrderDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\OrderStatusDataFixture;
use Shopsys\FrameworkBundle\Model\Order\OrderDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class OrderStatusFacadeTest extends DatabaseTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        $orderStatusFacade = $this->getContainer()->get(OrderStatusFacade::class);
        /* @var $orderStatusFacade \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade */
        $orderFacade = $this->getContainer()->get(OrderFacade::class);
        /* @var $orderFacade \Shopsys\FrameworkBundle\Model\Order\OrderFacade */

        $orderStatusData = new OrderStatusData();
        $orderStatusData->name = ['cs' => 'name'];
        $orderStatusToDelete = $orderStatusFacade->create($orderStatusData);
        $orderStatusToReplaceWith = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        /* @var $orderStatusToReplaceWith \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus */
        $order = $this->getReference(OrderDataFixture::ORDER_PREFIX . '1');
        /* @var $order \Shopsys\ShopBundle\Model\Order\Order */
        $orderDataFactory = $this->getContainer()->get(OrderDataFactoryInterface::class);
        /* @var $orderDataFactory \Shopsys\ShopBundle\Model\Order\OrderDataFactory */

        $orderData = $orderDataFactory->createFromOrder($order);
        $orderData->status = $orderStatusToDelete;
        $orderFacade->edit($order->getId(), $orderData);

        $orderStatusFacade->deleteById($orderStatusToDelete->getId(), $orderStatusToReplaceWith->getId());

        $em->refresh($order);

        $this->assertEquals($orderStatusToReplaceWith, $order->getStatus());
    }
}
