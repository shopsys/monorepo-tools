<?php

namespace ShopSys\CodingStandards\Sniff;

use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\ForbiddenFunctionsSniff;

final class ForbiddenExitSniff extends ForbiddenFunctionsSniff
{
    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var string[]|null[]
     */
    public $forbiddenFunctions = [
        'exit' => null,
    ];
}
