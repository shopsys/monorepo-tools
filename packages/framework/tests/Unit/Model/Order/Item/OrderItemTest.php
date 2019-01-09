<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Model\Order\Item;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Order\Item\Exception\WrongItemTypeException;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderPayment;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderTransport;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Transport\Transport;

class OrderItemTest extends TestCase
{
    public function testTransportCannotBeSetForWrongType(): void
    {
        $orderItem = $this->createOrderPayment();

        $this->expectException(WrongItemTypeException::class);
        $orderItem->setTransport($this->createTransportMock());
    }

    public function testTransportCannotBeGottenFromWrongType(): void
    {
        $orderItem = $this->createOrderPayment();

        $this->expectException(WrongItemTypeException::class);
        $orderItem->getTransport();
    }

    public function testEditTransportTypeEditsTransport(): void
    {
        $orderItem = $this->createOrderTransport();

        $orderItemData = new OrderItemData();
        $transport = $this->createTransportMock();
        $orderItemData->transport = $transport;
        $orderItem->edit($orderItemData);

        $this->assertSame($transport, $orderItem->getTransport());
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    private function createOrderPayment(): OrderItem
    {
        return new OrderPayment(
            $this->createOrderMock(),
            '',
            new Price(10, 12),
            0.2,
            1,
            $this->createPaymentMock()
        );
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    private function createOrderTransport(): OrderItem
    {
        return new OrderTransport(
            $this->createOrderMock(),
            '',
            new Price(10, 12),
            0.2,
            1,
            $this->createTransportMock()
        );
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Order|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createOrderMock(): MockObject
    {
        return $this->createMock(Order::class);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createTransportMock(): MockObject
    {
        return $this->createMock(Transport::class);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createPaymentMock(): MockObject
    {
        return $this->createMock(Payment::class);
    }
}
