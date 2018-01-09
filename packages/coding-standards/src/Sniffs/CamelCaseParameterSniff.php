<?php

declare(strict_types=1);

namespace ShopSys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;

/**
 * Inspired by Drupal Coding Standard https://www.drupal.org/project/coder/issues/2303963
 */
final class CamelCaseParameterSniff extends AbstractVariableSniff
{
    /**
     * @param int $position
     */
    protected function processVariable(File $file, $position): void
    {
        $tokens = $file->getTokens();

        $varName = ltrim($tokens[$position]['content'], '$');

        $phpReservedVars = ['_SERVER', '_GET', '_POST', '_REQUEST', '_SESSION', '_ENV', '_COOKIE', '_FILES', '_GLOBALS'];

        // If it's a php reserved var, then its ok.
        if (in_array($varName, $phpReservedVars, true)) {
            return;
        }

        if (Common::isCamelCaps($varName)) {
            return;
        }

        $file->addError(sprintf(
            'Variable "$%s" starts with underscore "_", but only $camelCase is allowed',
            $varName
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
