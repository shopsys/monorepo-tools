<?php

declare(strict_types=1);

namespace ShopSys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;

final class CamelCaseParameterSniff extends AbstractVariableSniff
{
    /**
     * @var string
     */
    private const NOT_LETTER_THEN_LETTER_PATTERN = '([^a-zA-Z]{1}(?<nextLetter>[A-Za-z]{1}))';

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

        $fix = $file->addFixableError(sprintf(
            'Variable "$%s" should be camel case', $variableName
        ), $position, self::class);

        if ($fix) {
            $this->fixVariableName($file, $position, $variableName);
        }
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

    /**
     * Changes:
     * _someVariable => someVariable
     * some_variable => someVariable
     */
    private function fixVariableName(File $file, int $position, string $variableName): void
    {
        $newVariableName = preg_replace_callback(
            '#(?<prefixLetter>[a-z]{1})?' . self::NOT_LETTER_THEN_LETTER_PATTERN . '#',
            function (array $match): string {
                $isFirstLetter = ! (bool) $match['prefixLetter'];
                if ($isFirstLetter) {
                    return $match['nextLetter'];
                }

                return $match['prefixLetter'] . strtoupper($match['nextLetter']);
            },
            $variableName
        );

        $file->fixer->replaceToken($position, '$' . $newVariableName);
    }
}
