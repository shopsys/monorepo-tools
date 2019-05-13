<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ConstantVisibilityRequiredSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register(): array
    {
        return [\T_CONST];
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param $constPosition
     */
    public function process(File $file, $constPosition): void
    {
        if (!$this->isConstInsideClass($file, $constPosition)) {
            return;
        }

        if ($this->isConstWithAccessModifier($file, $constPosition)) {
            return;
        }

        if ($this->isConstWithAccessAnnotation($file, $constPosition)) {
            return;
        }

        $file->addError('Constant must have access modifier', $constPosition, self::class);
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $constPosition
     * @return bool
     */
    private function isConstInsideClass(File $file, int $constPosition): bool
    {
        $classStartPosition = $file->findPrevious(\T_CLASS, $constPosition);
        if ($classStartPosition === false) {
            return false;
        }

        $tokens = $file->getTokens();
        $classEndPosition = $tokens[$classStartPosition]['scope_closer'];

        return $constPosition > $classStartPosition && $constPosition < $classEndPosition;
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $constPosition
     * @return bool
     */
    private function isConstWithAccessModifier(File $file, int $constPosition): bool
    {
        $previousTokenEndPosition = $this->findScopeSearchEndPosition($file, $constPosition);

        $accessModifierStartPosition = $file->findPrevious(Tokens::$scopeModifiers, $constPosition, $previousTokenEndPosition);

        return (bool)$accessModifierStartPosition;
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $constPosition
     * @return bool
     */
    private function isConstWithAccessAnnotation(File $file, int $constPosition): bool
    {
        $previousTokenEndPosition = $this->findScopeSearchEndPosition($file, $constPosition);

        $phpDocStartPosition = $file->findPrevious(\T_DOC_COMMENT_OPEN_TAG, $constPosition, $previousTokenEndPosition ?: 0);

        if ($phpDocStartPosition === false) {
            return false;
        }
        return $this->phpDocContainsAccessTag($file, $phpDocStartPosition);
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $phpDocStartPosition
     * @return bool
     */
    private function phpDocContainsAccessTag(File $file, int $phpDocStartPosition): bool
    {
        $tokens = $file->getTokens();

        $commentTagPositions = \array_reverse($tokens[$phpDocStartPosition]['comment_tags']);

        $lastPosition = $tokens[$phpDocStartPosition]['comment_closer'];

        foreach ($commentTagPositions as $commentTagPosition) {
            if ($tokens[$commentTagPosition]['content'] === '@access') {
                $possibleAccessModifierPosition = $file->findNext(\T_DOC_COMMENT_STRING, $commentTagPosition, $lastPosition);

                if (\preg_match('~(public|protected|private)~', $tokens[$possibleAccessModifierPosition]['content']) === 1) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $constPosition
     * @return int
     */
    private function findScopeSearchEndPosition(File $file, int $constPosition): int
    {
        return $file->findPrevious([\T_SEMICOLON, \T_CLOSE_CURLY_BRACKET], $constPosition) ?: 0;
    }
}
