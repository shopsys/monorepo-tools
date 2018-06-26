<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderTransportDataFactory implements OrderTransportDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportData
     */
    public function create(): OrderTransportData
    {
        return new OrderTransportData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransport $orderTransport
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportData
     */
    public function createFromOrderTransport(OrderTransport $orderTransport): OrderTransportData
    {
        $orderTransportData = new OrderTransportData();
        $this->fillFromOrderTransport($orderTransportData, $orderTransport);

        return $orderTransportData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransportData $orderTransportData
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderTransport $orderTransport
     */
    protected function fillFromOrderTransport(OrderTransportData $orderTransportData, OrderTransport $orderTransport)
    {
        $orderTransportData->name = $orderTransport->getName();
        $orderTransportData->priceWithVat = $orderTransport->getPriceWithVat();
        $orderTransportData->priceWithoutVat = $orderTransport->getPriceWithoutVat();
        $orderTransportData->vatPercent = $orderTransport->getVatPercent();
        $orderTransportData->quantity = $orderTransport->getQuantity();
        $orderTransportData->unitName = $orderTransport->getUnitName();
        $orderTransportData->catnum = $orderTransport->getCatnum();
        $orderTransportData->transport = $orderTransport->getTransport();
    }
}
