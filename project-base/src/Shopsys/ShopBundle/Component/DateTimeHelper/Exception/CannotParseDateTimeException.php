<?php

namespace Shopsys\ShopBundle\Component\DateTimeHelper\Exception;

use Exception;

class CannotParseDateTimeException extends Exception {

    /**
     * @param string $locale
     * @param string $time
     * @param \Exception|null $previous
     */
    public function __construct($format, $time, Exception $previous = null) {
        $message = sprintf(
            'Cannot parse string %s using format %s as DateTime.',
            var_export($time, true),
            var_export($format, true)
        );

        parent::__construct($message, 0, $previous);
    }

}
