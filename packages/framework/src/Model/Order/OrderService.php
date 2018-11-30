<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFactoryInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Price;

class OrderService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation
     */
    private $orderItemPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation
     */
    private $orderPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFactoryInterface
     */
    protected $orderProductFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation $orderItemPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation $orderPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFactoryInterface $orderProductFactory
     */
    public function __construct(
        OrderItemPriceCalculation $orderItemPriceCalculation,
        OrderPriceCalculation $orderPriceCalculation,
        OrderProductFactoryInterface $orderProductFactory
    ) {
        $this->orderItemPriceCalculation = $orderItemPriceCalculation;
        $this->orderPriceCalculation = $orderPriceCalculation;
        $this->orderProductFactory = $orderProductFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderEditResult
     */
    public function editOrder(Order $order, OrderData $orderData)
    {
        $orderTransportData = $orderData->orderTransport;
        $orderTransportData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderTransportData);
        $orderPaymentData = $orderData->orderPayment;
        $orderPaymentData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderPaymentData);

        $statusChanged = $order->getStatus() !== $orderData->status;
        $order->edit($orderData);

        $orderItemsWithoutTransportAndPaymentData = $orderData->itemsWithoutTransportAndPayment;

        $orderItemsToDelete = [];
        foreach ($order->getItemsWithoutTransportAndPayment() as $orderItem) {
            if (array_key_exists($orderItem->getId(), $orderItemsWithoutTransportAndPaymentData)) {
                $orderItemData = $orderItemsWithoutTransportAndPaymentData[$orderItem->getId()];
                $orderItemData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);
                $orderItem->edit($orderItemData);
            } else {
                $order->removeItem($orderItem);
                $orderItemsToDelete[] = $orderItem;
            }
        }

        $orderItemsToCreate = [];
        foreach ($orderData->getNewItemsWithoutTransportAndPayment() as $newOrderItemData) {
            $newOrderItemData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($newOrderItemData);
            $newOrderItem = $this->orderProductFactory->create(
                $order,
                $newOrderItemData->name,
                new Price(
                    $newOrderItemData->priceWithoutVat,
                    $newOrderItemData->priceWithVat
                ),
                $newOrderItemData->vatPercent,
                $newOrderItemData->quantity,
                $newOrderItemData->unitName,
                $newOrderItemData->catnum
            );
            $orderItemsToCreate[] = $newOrderItem;
        }

        $order->calculateTotalPrice($this->orderPriceCalculation);

        return new OrderEditResult($orderItemsToCreate, $orderItemsToDelete, $statusChanged);
    }
}
