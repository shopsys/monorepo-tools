<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Order\Status;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Order\Status\Exception\OrderStatusDeletionForbiddenException;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatus;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusData;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusService;

class OrderStatusServiceTest extends PHPUnit_Framework_TestCase {

    public function checkForDeleteProvider() {
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
    public function testCheckForDelete($statusType, $expectedException = null) {
        $orderStatusService = new OrderStatusService();
        $orderStatus = new OrderStatus(
            new OrderStatusData(['en' => 'orderStatusName']),
            $statusType
        );
        if ($expectedException !== null) {
            $this->setExpectedException($expectedException);
        }
        $orderStatusService->checkForDelete($orderStatus);
    }

}
