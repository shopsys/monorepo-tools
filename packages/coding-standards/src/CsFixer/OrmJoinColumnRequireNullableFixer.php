<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\CsFixer;

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class OrmJoinColumnRequireNullableFixer implements FixerInterface, DefinedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Annotations @ORM\ManyToOne and @ORM\OneToOne must have defined nullable option in @ORM\JoinColumn',
            [new CodeSample(
<<<'SAMPLE'
/**
 * @var \StdObject
 * @ORM\ManyToOne(targetEntity="StdObject")
 */
private $foo;
SAMPLE
            ), new CodeSample(
<<<'SAMPLE'
/**
 * @var \StdObject
 * @ORM\OneToOne(targetEntity="StdObject")
 */
private $foo;
SAMPLE
            )]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
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
    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens->findGivenKind(T_DOC_COMMENT) as $token) {
            /** @var Token $token */
            $doc = new DocBlock($token->getContent());
            foreach ($doc->getAnnotations() as $annotation) {
                if ($this->isRelationAnnotation($annotation)) {
                    $this->fixRelationAnnotation($doc, $annotation);
                    $token->setContent($doc->getContent());
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Shopsys/orm_join_column_require_nullable';
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
    public function supports(SplFileInfo $file): bool
    {
        return preg_match('/\.php$/ui', $file->getFilename()) === 1;
    }

    /**
     * @param \PhpCsFixer\DocBlock\Annotation $annotation
     */
    private function isRelationAnnotation(Annotation $annotation): bool
    {
        return preg_match('~@ORM\\\(ManyToOne|OneToOne)\\(~', $annotation->getContent()) === 1;
    }

    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     * @param \PhpCsFixer\DocBlock\Annotation $relationAnnotation
     */
    private function fixRelationAnnotation(DocBlock $doc, Annotation $relationAnnotation): void
    {
        $joinColumnAnnotation = $this->findJoinColumnAnnotation($doc);
        if ($joinColumnAnnotation === null) {
            $this->addJoinColumnAnnotation($doc, $relationAnnotation);
        } elseif (preg_match('~(,|\\()\\s*nullable\\s*=~', $joinColumnAnnotation->getContent()) !== 1) {
            $this->extendJoinColumnAnnotation($doc, $joinColumnAnnotation);
        }
    }

    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     */
    private function findJoinColumnAnnotation(DocBlock $doc): ?Annotation
    {
        foreach ($doc->getAnnotations() as $annotation) {
            if (preg_match('~@ORM\\\JoinColumn\\(~', $annotation->getContent()) === 1) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     * @param \PhpCsFixer\DocBlock\Annotation $relationAnnotation
     */
    private function addJoinColumnAnnotation(DocBlock $doc, Annotation $relationAnnotation): void
    {
        $matches = null;
        preg_match_all('~\\s*\*~', $relationAnnotation->getContent(), $matches);
        $lastLine = $doc->getLine($relationAnnotation->getEnd());
        $lastLine->setContent($lastLine->getContent() . $matches[0][0] . ' @ORM\JoinColumn(nullable=false)' . "\n");
    }

    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     * @param \PhpCsFixer\DocBlock\Annotation $joinColumnAnnotation
     */
    private function extendJoinColumnAnnotation(DocBlock $doc, Annotation $joinColumnAnnotation): void
    {
        $firstLine = $doc->getLine($joinColumnAnnotation->getStart());
        if (preg_match('~\\)\\s*$~', $firstLine->getContent()) === 1) {
            $firstLine->setContent(preg_replace(
                '~(@ORM\\\JoinColumn\\()~',
                '$1nullable=false, ',
                $firstLine->getContent()
            ));
        } else {
            $matches = null;
            preg_match_all('~\\s*\*~', $joinColumnAnnotation->getContent(), $matches);
            $newText = "\n" . $matches[0][0] . '     nullable=false,';
            $firstLine->setContent(preg_replace('~(@ORM\\\JoinColumn\\()~', '$1' . $newText, $firstLine->getContent()));
        }
    }
}
