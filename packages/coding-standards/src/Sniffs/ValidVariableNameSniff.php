<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;

/*
 * \PHP_CodeSniffer\Standards\Squiz\Sniffs\NamingConventions\ValidVariableNameSniff
 * does not report method parameters in $_var format as an violation but it should.
 * It also skips checking of private members when PrivateNoUnderscore property is disabled.
 *
 * see https://github.com/squizlabs/PHP_CodeSniffer/issues/1851
 *
 * This sniff provides the missing functionality and is intended to be used as an addition to the sniff mentioned above.
 */
final class ValidVariableNameSniff extends AbstractVariableSniff
{
    /**
     * @param int $position
     */
    protected function processVariable(File $file, $position): void
    {
        $errorMessageFormat = 'Variable "$%s" should be camel case';
        $this->checkCamelCaseFormatViolation($file, $position, $errorMessageFormat);
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
        $errorMessageFormat = 'Class member variable "$%s" should be camel case';
        $this->checkCamelCaseFormatViolation($file, $position, $errorMessageFormat);
    }

    private function checkCamelCaseFormatViolation(File $file, int $position, string $errorMessageFormat): void
    {
        $currentToken = $file->getTokens()[$position];

        $variableName = ltrim($currentToken['content'], '$');
        if (Common::isCamelCaps($variableName)) {
            return;
        }

        $file->addError(sprintf(
            $errorMessageFormat,
            $variableName
        ), $position, self::class);
    }
}
