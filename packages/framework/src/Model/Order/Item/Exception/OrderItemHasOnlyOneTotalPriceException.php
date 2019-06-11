<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item\Exception;

use RuntimeException;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Throwable;

class OrderItemHasOnlyOneTotalPriceException extends RuntimeException implements OrderItemException
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $totalPriceWithVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $totalPriceWithoutVat
     * @param \Throwable|null $previous
     */
    public function __construct(?Money $totalPriceWithVat, ?Money $totalPriceWithoutVat, ?Throwable $previous = null)
    {
        $message = sprintf(
            'Order item has only one of its total prices set: %s with VAT / %s without VAT',
            $totalPriceWithVat !== null ? $totalPriceWithVat->getAmount() : 'NULL',
            $totalPriceWithoutVat !== null ? $totalPriceWithoutVat->getAmount() : 'NULL'
        );

        parent::__construct($message, 0, $previous);
    }
}
