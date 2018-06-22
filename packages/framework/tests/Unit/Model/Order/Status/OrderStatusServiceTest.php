<?php

namespace Tests\FrameworkBundle\Unit\Model\Order\Status;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData;
use Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusService;

class OrderStatusServiceTest extends TestCase
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
     */
    public function testCheckForDelete($statusType, $expectedException = null)
    {
        $orderStatusService = new OrderStatusService();
        $orderStatusData = new OrderStatusData();
        $orderStatusData->name = ['en' => 'orderStatusName'];
        $orderStatus = new OrderStatus($orderStatusData, $statusType);
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }
        $orderStatusService->checkForDelete($orderStatus);
    }
}
