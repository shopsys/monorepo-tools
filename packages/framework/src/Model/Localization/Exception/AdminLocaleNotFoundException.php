<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Localization\Exception;

use Exception;
use RuntimeException;

class AdminLocaleNotFoundException extends RuntimeException implements LocalizationException
{
    /**
     * @param string $adminLocale
     * @param string[] $possibleLocales
     * @param \Exception|null $previous
     */
    public function __construct(string $adminLocale, array $possibleLocales, Exception $previous = null)
    {
        $message = sprintf(
            'You tried to use administration in "%1$s" locale, but you have registered only ["%2$s"].'
            . ' Either register "%1$s" as a locale with some domain or use one of ["%2$s"] as administration locale.',
            $adminLocale,
            implode('","', $possibleLocales)
        );
        parent::__construct($message, 0, $previous);
    }
}
