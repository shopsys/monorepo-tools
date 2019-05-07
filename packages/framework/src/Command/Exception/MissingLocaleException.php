<?php

namespace Shopsys\FrameworkBundle\Command\Exception;

use Exception;
use Throwable;

class MissingLocaleException extends Exception
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @param mixed $missingLocale
     * @param \Throwable|null $previous
     */
    public function __construct($missingLocale, Throwable $previous = null)
    {
        $message = sprintf(
            'It looks like your operating system does not support locale "%s". '
                . 'Please visit docs/installation/native-installation-troubleshooting.md for more details.',
            $missingLocale
        );

        $this->locale = $missingLocale;

        parent::__construct($message, 0, $previous);
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
