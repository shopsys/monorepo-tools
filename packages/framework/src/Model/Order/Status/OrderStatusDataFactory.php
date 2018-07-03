<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status;

class OrderStatusDataFactory implements OrderStatusDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData
     */
    public function create(): OrderStatusData
    {
        return new OrderStatusData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $orderStatus
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData
     */
    public function createFromOrderStatus(OrderStatus $orderStatus): OrderStatusData
    {
        $orderStatusData = new OrderStatusData();
        $this->fillFromOrderStatus($orderStatusData, $orderStatus);

        return $orderStatusData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatusData $orderStatusData
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $orderStatus
     */
    protected function fillFromOrderStatus(OrderStatusData $orderStatusData, OrderStatus $orderStatus)
    {
        $translations = $orderStatus->getTranslations();
        $names = [];
        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
        }
        $orderStatusData->name = $names;
    }
}
