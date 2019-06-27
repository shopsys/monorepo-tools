<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use BadMethodCallException;
use Shopsys\FrameworkBundle\Model\Order\Item\Exception\OrderItemPriceCalculationNotInjectedException;
use Shopsys\FrameworkBundle\Model\Order\Item\Exception\OrderItemUnitPricesAreInconsistentButTotalsAreNotForcedException;

class OrderItemDataFactory implements OrderItemDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation|null
     */
    protected $orderItemPriceCalculation;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation|null $orderItemPriceCalculation
     */
    public function __construct(?OrderItemPriceCalculation $orderItemPriceCalculation = null)
    {
        $this->orderItemPriceCalculation = $orderItemPriceCalculation;
    }

    /**
     * @required
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation $orderItemPriceCalculation
     * @deprecated Will be replaced with constructor injection in the next major release
     */
    public function setOrderItemPriceCalculation(OrderItemPriceCalculation $orderItemPriceCalculation)
    {
        if ($this->orderItemPriceCalculation !== null && $this->orderItemPriceCalculation !== $orderItemPriceCalculation) {
            throw new BadMethodCallException(sprintf('Method "%s" has been already called and cannot be called multiple times.', __METHOD__));
        }

        if ($this->orderItemPriceCalculation === null) {
            @trigger_error(sprintf('The %s() method is deprecated and will be removed in the next major. Use the constructor injection instead.', __METHOD__), E_USER_DEPRECATED);

            $this->orderItemPriceCalculation = $orderItemPriceCalculation;
        }
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

        $orderItemTotalPrice = $this->getOrderItemPriceCalculation()->calculateTotalPrice($orderItem);
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

        $calculatedPriceWithoutVat = $this->getOrderItemPriceCalculation()->calculatePriceWithoutVat($orderItemData);
        if (!$orderItemData->priceWithoutVat->equals($calculatedPriceWithoutVat)) {
            throw new OrderItemUnitPricesAreInconsistentButTotalsAreNotForcedException($orderItem, $calculatedPriceWithoutVat);
        }

        return true;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation
     */
    protected function getOrderItemPriceCalculation(): OrderItemPriceCalculation
    {
        if ($this->orderItemPriceCalculation === null) {
            throw new OrderItemPriceCalculationNotInjectedException(static::class, 'setOrderItemPriceCalculation');
        }

        return $this->orderItemPriceCalculation;
    }
}
