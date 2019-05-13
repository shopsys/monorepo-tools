<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\CsFixer\Phpdoc;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NoUselessAccessFixer implements FixerInterface, DefinedFixerInterface
{
    private const ACCESS_TOKENS = [
        \T_PUBLIC,
        \T_PROTECTED,
        \T_PRIVATE,
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            '`@access` annotations should be omitted from PHPDoc when it is useless',
            [
                new CodeSample(
                    '<?php
class Foo
{
    /**
     * @internal
     * @access private
     */
    private $bar;
}
'
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT) && $tokens->isAnyTokenKindsFound(self::ACCESS_TOKENS);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
        $allDocBlocks = $this->findAllDocBlocks($tokens);

        foreach ($allDocBlocks as $position => $docBlock) {
            if (!$this->areThereAnyAccessAnnotation($docBlock)) {
                continue;
            }

            if ($this->followingTokenIsAccessModifier($tokens, $position)) {
                $this->removeAllAccessAnnotations($docBlock);
                $this->rewriteDocBlock($tokens, $docBlock, $position);
            }

            if ($this->areThereAnyEmptyAccessAnnotation($docBlock)) {
                $this->removeEmptyAccessAnnotations($docBlock);
                $this->rewriteDocBlock($tokens, $docBlock, $position);
            }
        }
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return \PhpCsFixer\DocBlock\DocBlock[]
     */
    private function findAllDocBlocks(Tokens $tokens): array
    {
        return \array_map(function (Token $token) {
            return new DocBlock($token->getContent());
        }, $tokens->findGivenKind(\T_DOC_COMMENT));
    }

    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $docBlock
     * @return bool
     */
    private function areThereAnyAccessAnnotation(DocBlock $docBlock): bool
    {
        $annotations = $docBlock->getAnnotationsOfType('access');

        return !empty($annotations);
    }

    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $docBlock
     * @return bool
     */
    private function areThereAnyEmptyAccessAnnotation(DocBlock $docBlock): bool
    {
        $annotations = $docBlock->getAnnotationsOfType('access');

        foreach ($annotations as $annotation) {
            if (\preg_match('~(public|protected|private)~', $annotation->getContent()) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $docBlock
     */
    private function removeEmptyAccessAnnotations(DocBlock $docBlock): void
    {
        $annotations = $docBlock->getAnnotationsOfType('access');

        foreach ($annotations as $annotation) {
            if (\preg_match('~(public|protected|private)~', $annotation->getContent()) === 0) {
                $annotation->remove();
            }
        }
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $position
     * @return bool
     */
    private function followingTokenIsAccessModifier(Tokens $tokens, int $position): bool
    {
        $nextMeaningfulTokenId = $tokens[$tokens->getNextMeaningfulToken($position)]->getId();

        return \in_array($nextMeaningfulTokenId, self::ACCESS_TOKENS, true);
    }

    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $docBlock
     */
    private function removeAllAccessAnnotations(DocBlock $docBlock): void
    {
        $annotations = $docBlock->getAnnotationsOfType('access');

        foreach ($annotations as $annotation) {
            $annotation->remove();
        }
    }

    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param \PhpCsFixer\DocBlock\DocBlock $docBlock
     * @param int $position
     */
    private function rewriteDocBlock(Tokens $tokens, DocBlock $docBlock, int $position): void
    {
        if ($docBlock->getContent() === '' || $this->isDocBlockEmpty($docBlock)) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($position);
        } else {
            $tokens[$position] = new Token([T_DOC_COMMENT, $docBlock->getContent()]);
        }
    }

    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $docBlock
     * @return bool
     */
    private function isDocBlockEmpty(DocBlock $docBlock): bool
    {
        foreach ($docBlock->getLines() as $line) {
            if ($line->containsUsefulContent()) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Shopsys/phpdoc_no_useless_access';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file): bool
    {
        return true;
    }
}
