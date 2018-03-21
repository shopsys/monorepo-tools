<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Inspired by
 * @see https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/blob/master/WordPress/Sniffs/VIP/SuperGlobalInputUsageSniff.php
 */
final class ForbiddenSuperGlobalSniff implements Sniff
{
    /**
     * @var string[]
     */
    private $superglobalVariables = [
        '$_COOKIE', '$_GET', '$_FILES', '$_POST', '$_REQUEST', '$_SERVER', '$_SESSION', '$_ENV', '$GLOBALS',
    ];

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_VARIABLE];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $currentToken = $file->getTokens()[$position];

        if (!in_array($currentToken['content'], $this->superglobalVariables, true)) {
            return;
        }

        $file->addError(
            sprintf('Super global "%s" is forbidden', $currentToken['content']),
            $position,
            self::class
        );
    }
}
