<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item\Exception;

use RuntimeException;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Throwable;

class OrderItemPriceCalculationNotInjectedException extends RuntimeException implements OrderItemException
{
    /**
     * @param string $className
     * @param string $setterName
     * @param \Throwable|null $previous
     */
    public function __construct(string $className, string $setterName, ?Throwable $previous = null)
    {
        $message = sprintf('The instance of %s has not been injected into %s using the "%s" setter.', OrderItemPriceCalculation::class, $className, $setterName);
        $message .= ' Either get the class instance from the DIC with enabled autowiring or call the setter directly during class instantiation.';

        parent::__construct($message, 0, $previous);
    }
}
