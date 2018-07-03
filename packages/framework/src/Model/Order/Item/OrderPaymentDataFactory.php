<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

class OrderPaymentDataFactory implements OrderPaymentDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentData
     */
    public function create(): OrderPaymentData
    {
        return new OrderPaymentData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderPayment $orderPayment
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentData
     */
    public function createFromOrderPayment(OrderPayment $orderPayment): OrderPaymentData
    {
        $orderPaymentData = new OrderPaymentData();
        $this->fillFromOrderPayment($orderPaymentData, $orderPayment);

        return $orderPaymentData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentData $orderPaymentData
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderPayment $orderPayment
     */
    protected function fillFromOrderPayment(OrderPaymentData $orderPaymentData, OrderPayment $orderPayment)
    {
        $orderPaymentData->name = $orderPayment->getName();
        $orderPaymentData->priceWithVat = $orderPayment->getPriceWithVat();
        $orderPaymentData->priceWithoutVat = $orderPayment->getPriceWithoutVat();
        $orderPaymentData->vatPercent = $orderPayment->getVatPercent();
        $orderPaymentData->quantity = $orderPayment->getQuantity();
        $orderPaymentData->unitName = $orderPayment->getUnitName();
        $orderPaymentData->catnum = $orderPayment->getCatnum();
        $orderPaymentData->payment = $orderPayment->getPayment();
    }
}
