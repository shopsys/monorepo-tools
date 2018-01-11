<?php

declare(strict_types=1);

namespace ShopSys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;

final class CamelCaseParameterSniff extends AbstractVariableSniff
{
    /**
     * @param int $position
     */
    protected function processVariable(File $file, $position): void
    {
        $currentToken = $file->getTokens()[$position];

        $variableName = ltrim($currentToken['content'], '$');
        if (Common::isCamelCaps($variableName)) {
            return;
        }

        $file->addError(sprintf(
            'Variable "$%s" should be camel case',
            $variableName
        ), $position, self::class);
    }

    /**
     * @param int $position
     */
    protected function processVariableInString(File $file, $position): void
    {
    }

    /**
     * @param int $position
     */
    protected function processMemberVar(File $file, $position): void
    {
    }
}
