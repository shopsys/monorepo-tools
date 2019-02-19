<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ForbiddenDoctrineInheritanceSniff implements Sniff
{
    /**
     * @return int[]
     */
    public function register()
    {
        return [T_CLASS];
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $classPosition
     */
    public function process(File $file, $classPosition)
    {
        $phpDocStartPosition = $file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $classPosition);
        if ($phpDocStartPosition === false) {
            return;
        }

        $phpDocTags = $this->findPhpDocTags($file, $classPosition, $phpDocStartPosition);
        foreach ($phpDocTags as $position => $token) {
            if ($this->isDoctrineInheritanceAnnotation($token)) {
                $message = 'It is forbidden to use Doctrine inheritance mapping because it causes problems during entity extension. Such problem with `OrderItem` was resolved during making OrderItem extendable #715.';
                $file->addError(
                    $message,
                    $position,
                    self::class
                );
            }
        }
    }

    /**
     * @param \PHP_CodeSniffer\Files\File $file
     * @param int $classPosition
     * @param int $phpDocStartPosition
     * @return array
     */
    private function findPhpDocTags(File $file, int $classPosition, int $phpDocStartPosition): array
    {
        $phpDocEndPosition = $file->findPrevious(T_DOC_COMMENT_CLOSE_TAG, $classPosition);

        $result = [];
        $tokens = $file->getTokens();
        for ($i = $phpDocStartPosition; $i < $phpDocEndPosition; $i++) {
            if ($tokens[$i]['code'] === T_DOC_COMMENT_TAG) {
                $result[$i] = $tokens[$i];
            }
        }

        return $result;
    }

    /**
     * @param array $token
     */
    private function isDoctrineInheritanceAnnotation(array $token)
    {
        $content = $token['content'];
        return preg_match('~^.*ORM.*InheritanceType~', $content) === 1;
    }
}
