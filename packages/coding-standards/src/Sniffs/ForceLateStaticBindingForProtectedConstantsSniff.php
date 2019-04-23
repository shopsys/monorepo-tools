<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\ConstantHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ForceLateStaticBindingForProtectedConstantsSniff implements Sniff
{
    /**
     * {@inheritdoc}
     */
    public function register(): array
    {
        return [\T_CLASS];
    }

    /**
     * {@inheritdoc}
     */
    public function process(File $file, $classPosition)
    {
        $protectedConstants = $this->getAllProtectedConstantsInClass($file);

        $selfPositions = TokenHelper::findNextAll($file, \T_SELF, $classPosition);

        foreach ($selfPositions as $selfPosition) {
            $constantName = $this->findConstantNameFromSelfCall($file, $selfPosition);

            if ($constantName === null) {
                continue;
            }

            if (\in_array($constantName, $protectedConstants, true)) {
                $file->addFixableError(
                    'For better extensibility use late static binding.',
                    $selfPosition,
                    self::class
                );

                $file->fixer->beginChangeset();
                $file->fixer->replaceToken($selfPosition, 'static');
                $file->fixer->endChangeset();
            }
        }
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $selfPosition
     * @return string|null
     */
    private function findConstantNameFromSelfCall(File $file, int $selfPosition): ?string
    {
        $tokens = $file->getTokens();

        $doubleColonPosition = TokenHelper::findNextEffective($file, $selfPosition + 1);
        if ($tokens[$doubleColonPosition]['code'] !== T_DOUBLE_COLON) {
            return null;
        }

        $stringPosition = TokenHelper::findNextEffective($file, $doubleColonPosition + 1);
        if ($tokens[$stringPosition]['code'] !== T_STRING) {
            return null;
        }

        if (strtolower($tokens[$stringPosition]['content']) === 'class') {
            return null;
        }

        $positionAfterString = TokenHelper::findNextEffective($file, $stringPosition + 1);
        if ($tokens[$positionAfterString]['code'] === T_OPEN_PARENTHESIS) {
            return null;
        }

        return $tokens[$stringPosition]['content'];
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @return string[]
     */
    private function getAllProtectedConstantsInClass(File $file): array
    {
        $constPositions = TokenHelper::findNextAll($file, \T_CONST, 0);

        $protectedConstants = [];

        foreach ($constPositions as $constPosition) {
            if ($this->isProtectedVisibility($file, $constPosition)) {
                $protectedConstants[] = ConstantHelper::getName($file, $constPosition);

                continue;
            }

            if ($this->hasProtectedAccess($file, $constPosition)) {
                $protectedConstants[] = ConstantHelper::getName($file, $constPosition);

                continue;
            }
        }

        return $protectedConstants;
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $constPosition
     * @return bool
     */
    private function isProtectedVisibility(File $file, int $constPosition): bool
    {
        $protectedModifierPosition = TokenHelper::findPreviousLocal($file, \T_PROTECTED, $constPosition);

        return $protectedModifierPosition !== null;
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $constPosition
     * @return bool
     */
    private function hasProtectedAccess(File $file, int $constPosition): bool
    {
        $accessAnnotations = AnnotationHelper::getAnnotationsByName($file, $constPosition, '@access');
        foreach ($accessAnnotations as $accessAnnotation) {
            if ($accessAnnotation->getContent() === 'protected') {
                return true;
            }
        }

        return false;
    }
}
