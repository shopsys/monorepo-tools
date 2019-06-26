<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item\Exception;

use RuntimeException;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Throwable;

class OrderItemUnitPricesAreInconsistentButTotalsAreNotForcedException extends RuntimeException implements OrderItemException
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem $orderItem
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $calculatedPriceWithoutVat
     * @param \Throwable|null $previous
     */
    public function __construct(OrderItem $orderItem, Money $calculatedPriceWithoutVat, ?Throwable $previous = null)
    {
        $message = sprintf(
            'The order item %s has unit prices inconsistent with price calculation (%s with VAT and %s without VAT). Either the unit price without VAT should be %s, or the total prices should be forced.',
            $orderItem->getId() !== null ? sprintf('with ID %d', $orderItem->getId()) : 'without ID',
            $orderItem->getPriceWithVat()->getAmount(),
            $orderItem->getPriceWithoutVat()->getAmount(),
            $calculatedPriceWithoutVat->getAmount()
        );

        parent::__construct($message, 0, $previous);
    }
}
