<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Shopsys\FrameworkBundle\Model\Order\Item\Exception\OrderItemUnitPricesAreInconsistentButTotalsAreNotForcedException;

class OrderItemDataFactory implements OrderItemDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation
     */
    protected $orderItemPriceCalculation;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation $orderItemPriceCalculation
     */
    public function __construct(OrderItemPriceCalculation $orderItemPriceCalculation)
    {
        $this->orderItemPriceCalculation = $orderItemPriceCalculation;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData
     */
    public function create(): OrderItemData
    {
        return new OrderItemData();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData
     */
    public function createFromOrderItem(OrderItem $orderItem): OrderItemData
    {
        $orderItemData = new OrderItemData();
        $this->fillFromOrderItem($orderItemData, $orderItem);
        $this->addFieldsByOrderItemType($orderItemData, $orderItem);

        return $orderItemData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData $orderItemData
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     */
    protected function fillFromOrderItem(OrderItemData $orderItemData, OrderItem $orderItem)
    {
        $orderItemData->name = $orderItem->getName();
        $orderItemData->priceWithVat = $orderItem->getPriceWithVat();
        $orderItemData->priceWithoutVat = $orderItem->getPriceWithoutVat();

        $orderItemTotalPrice = $this->orderItemPriceCalculation->calculateTotalPrice($orderItem);
        $orderItemData->totalPriceWithVat = $orderItemTotalPrice->getPriceWithVat();
        $orderItemData->totalPriceWithoutVat = $orderItemTotalPrice->getPriceWithoutVat();

        $orderItemData->vatPercent = $orderItem->getVatPercent();
        $orderItemData->quantity = $orderItem->getQuantity();
        $orderItemData->unitName = $orderItem->getUnitName();
        $orderItemData->catnum = $orderItem->getCatnum();

        $orderItemData->usePriceCalculation = $this->isUsingPriceCalculation($orderItemData, $orderItem);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData $orderItemData
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     */
    protected function addFieldsByOrderItemType(OrderItemData $orderItemData, OrderItem $orderItem): void
    {
        if ($orderItem->isTypeTransport()) {
            $orderItemData->transport = $orderItem->getTransport();
        } elseif ($orderItem->isTypePayment()) {
            $orderItemData->payment = $orderItem->getPayment();
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData $orderItemData
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     * @return bool
     */
    protected function isUsingPriceCalculation(OrderItemData $orderItemData, OrderItem $orderItem): bool
    {
        if ($orderItem->hasForcedTotalPrice()) {
            return false;
        }

        $calculatedPriceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);
        if (!$orderItemData->priceWithoutVat->equals($calculatedPriceWithoutVat)) {
            throw new OrderItemUnitPricesAreInconsistentButTotalsAreNotForcedException($orderItem, $calculatedPriceWithoutVat);
        }

        return true;
    }
}
