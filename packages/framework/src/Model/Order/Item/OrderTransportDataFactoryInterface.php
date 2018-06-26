<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

interface OrderTransportDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportData
     */
    public function create(): OrderTransportData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransport $orderTransport
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportData
     */
    public function createFromOrderTransport(OrderTransport $orderTransport): OrderTransportData;
}
