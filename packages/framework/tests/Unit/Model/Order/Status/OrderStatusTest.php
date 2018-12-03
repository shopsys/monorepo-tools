<?php

namespace Tests\FrameworkBundle\Unit\Model\Order\Status;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData;

class OrderStatusTest extends TestCase
{
    public function checkForDeleteProvider()
    {
        return [
            ['type' => OrderStatus::TYPE_NEW, 'expectedException' => OrderStatusDeletionForbiddenException::class],
            ['type' => OrderStatus::TYPE_IN_PROGRESS, 'expectedException' => null],
            ['type' => OrderStatus::TYPE_DONE, 'expectedException' => OrderStatusDeletionForbiddenException::class],
            ['type' => OrderStatus::TYPE_CANCELED, 'expectedException' => OrderStatusDeletionForbiddenException::class],
        ];
    }

    /**
     * @dataProvider checkForDeleteProvider
     * @param mixed $statusType
     * @param mixed|null $expectedException
     */
    public function testCheckForDelete($statusType, $expectedException = null)
    {
        $orderStatusData = new OrderStatusData();
        $orderStatusData->name = ['en' => 'orderStatusName'];
        $orderStatus = new OrderStatus($orderStatusData, $statusType);
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }
        $orderStatus->checkForDelete();
    }
}
