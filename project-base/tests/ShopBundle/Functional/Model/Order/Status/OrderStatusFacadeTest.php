<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Model\Order\Status;

use Shopsys\FrameworkBundle\Model\Order\OrderDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade;
use Shopsys\ShopBundle\DataFixtures\Demo\OrderDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\OrderStatusDataFixture;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class OrderStatusFacadeTest extends TransactionFunctionalTestCase
{
    public function testDeleteByIdAndReplace()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusFacade $orderStatusFacade */
        $orderStatusFacade = $this->getContainer()->get(OrderStatusFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade $orderFacade */
        $orderFacade = $this->getContainer()->get(OrderFacade::class);

        $orderStatusData = new OrderStatusData();
        $orderStatusData->name = ['cs' => 'name'];
        $orderStatusToDelete = $orderStatusFacade->create($orderStatusData);
        /** @var \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $orderStatusToReplaceWith */
        $orderStatusToReplaceWith = $this->getReference(OrderStatusDataFixture::ORDER_STATUS_NEW);
        /** @var \Shopsys\ShopBundle\Model\Order\Order $order */
        $order = $this->getReference(OrderDataFixture::ORDER_PREFIX . '1');
        /** @var \Shopsys\ShopBundle\Model\Order\OrderDataFactory $orderDataFactory */
        $orderDataFactory = $this->getContainer()->get(OrderDataFactoryInterface::class);

        $orderData = $orderDataFactory->createFromOrder($order);
        $orderData->status = $orderStatusToDelete;
        $orderFacade->edit($order->getId(), $orderData);

        $orderStatusFacade->deleteById($orderStatusToDelete->getId(), $orderStatusToReplaceWith->getId());

        $em->refresh($order);

        $this->assertEquals($orderStatusToReplaceWith, $order->getStatus());
    }
}
