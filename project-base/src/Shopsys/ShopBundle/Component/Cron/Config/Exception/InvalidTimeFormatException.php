<?php

namespace Shopsys\ShopBundle\Component\Cron\Config\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Cron\Config\Exception\CronConfigException;

class InvalidTimeFormatException extends Exception implements CronConfigException
{

    /**
     * @param string $timeString
     * @param int $maxValue
     * @param int $divisibleBy
     * @param \Exception $previous
     */
    public function __construct($timeString, $maxValue, $divisibleBy, Exception $previous = null) {
        parent::__construct(
            'Time configuration "' . $timeString . '" is invalid. '
            . 'Must by divisible by ' . $divisibleBy . ' and less or equal than ' . $maxValue,
            0,
            $previous
        );
    }
}
