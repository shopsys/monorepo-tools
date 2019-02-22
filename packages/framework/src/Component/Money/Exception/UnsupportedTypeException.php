<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Money\Exception;

use InvalidArgumentException;
use Throwable;

class UnsupportedTypeException extends InvalidArgumentException implements MoneyException
{
    /**
     * @param mixed $value
     * @param string[] $supportedTypes
     * @param \Throwable|null $previous
     */
    public function __construct($value, array $supportedTypes, Throwable $previous = null)
    {
        $message = sprintf('Expected one of: "%s"', implode('", "', $supportedTypes));
        $message .= sprintf(', "%s" given.', \is_object($value) ? \get_class($value) : \gettype($value));

        parent::__construct($message, 0, $previous);
    }
}
