<?php

namespace Shopsys\ShopBundle\Command\Exception;

use Exception;

class MissingLocaleAggregateException extends Exception
{
    /**
     * @param \Shopsys\ShopBundle\Command\Exception\MissingLocaleException[] $missingLocaleExceptions
     */
    public function __construct(array $missingLocaleExceptions)
    {
        $missingLocales = [];
        foreach ($missingLocaleExceptions as $missingLocaleException) {
            $missingLocales[] = $missingLocaleException->getLocale();
        }

        $message = sprintf(
            'It looks like your operating system does not support these locales: %s. '
                . 'Please visit docs/introduction/installation-guide.md for more details.',
            '"' . implode('", "', $missingLocales) . '"'
        );

        parent::__construct($message, $missingLocaleExceptions[0]->getCode(), $missingLocaleExceptions[0]);
    }
}
